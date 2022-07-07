/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/Form/other_properties.twig
 *  Template/Elements/Form/core_properties.twig
 *  Template/Elements/Form/meta.twig
 *
 * <property-view> component used for ModulesPage -> View
 *
 * Component that wraps group of properties in the object View
 *
 * @prop {Boolean} tabOpen determines whether the property content is visible or not
 * @prop {String} label label of the property view
 *
 */

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

const STORAGE_TABS_KEY = 'tabs_open_' + BEDITA.currentModule.name;

export default {
    components: {
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        RelationView: () => import(/* webpackChunkName: "relation-view" */'app/components/relation-view/relation-view'),
        ResourceRelationView: () => import(/* webpackChunkName: "resource-relation-view" */'app/components/relation-view/resource-relation-view'),
        MapView: () => import(/* webpackChunkName: "map-view" */'app/components/map-view'),
        DateRangesView: () => import(/* webpackChunkName: "date-ranges-view" */'app/components/date-ranges-view/date-ranges-view'),
        History: () => import(/* webpackChunkName: "history" */'app/components/history/history'),
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */'app/components/coordinates-view'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
    },

    props: {
        tabOpen: {
            type: Boolean,
            default: false,
        },
        tabName: {
            type: String,
            default: null,
        },
        tabOpenAtStart: {
            type: Boolean,
            default: null,
        },
        isDefaultOpen: {
            type: Boolean,
            default: false,
        },
        preCount: {
            type: Number,
            default: -1,
        },
        uploadableNum: {
            type: String,
            default: '0',
        },
        object: {
            type: Object,
            default: function() {
                return {};
            },
        }
    },

    data() {
        return {
            isOpen: this.isDefaultOpen,
            isLoading: false,
            totalObjects: 0,
            dataList: parseInt(this.uploadableNum) == 0,
            userInfoLoaded: false,
        }
    },

    async mounted() {

        if (this.tabOpenAtStart !== null) {
            this.isOpen = this.tabOpenAtStart;
            return;
        }
        this.isOpen = this.checkTabOpen();
        if (this.preCount >= 0) {
            this.totalObjects = this.preCount;
        }

        // load user info in meta fields (created_by and modified_by )
        if (this.tabName === 'meta' && this.isOpen) {
            await this.loadInfoUsers();
        }
    },

    watch: {
        tabOpen() {
            this.isOpen = this.tabOpen;
        },
        isOpen() {
            if (this.tabName === 'meta' && this.isOpen && !this.userInfoLoaded) {
                this.loadInfoUsers();
            }
        }
    },

    methods: {
        toggleVisibility() {
            this.isOpen = !this.isOpen;
            this.checkLoadRelated();
            this.updateStorage();
        },
        onToggleLoading(status) {
            this.isLoading = status;
        },
        onCount(n, force = false) {
            if (this.totalObjects === 0 || force) {
                this.totalObjects = n;
            }
        },
        switchBlockView() {
            this.dataList = false;
        },
        switchListView() {
            this.dataList = true;
        },
        checkTabOpen() {
            if (!this.tabName) {
                return false;
            }
            let tabs = this.readTabsOpen();
            return (tabs.indexOf(this.tabName) >= 0);
        },
        readTabsOpen() {
            if (localStorage.getItem(STORAGE_TABS_KEY)) {
                return JSON.parse(localStorage.getItem(STORAGE_TABS_KEY));
            }
            // if `isDefaultOpen` is true, on the first access, this tab is saved as open
            // Note: there can be only one tab having `tabName` and `isDefaultOpen` set as true
            if (this.isDefaultOpen) {
                localStorage.setItem(STORAGE_TABS_KEY, JSON.stringify([this.tabName]));
                return [this.tabName];
            };

            return [];
        },
        updateStorage() {
            if (!this.tabName) {
                return;
            }
            let tabs = this.readTabsOpen();
            const pos = tabs.indexOf(this.tabName);
            if (this.isOpen && pos < 0) {
                tabs.push(this.tabName);
            } else if (!this.isOpen && pos >= 0) {
                tabs.splice(pos, 1);
            }
            localStorage.setItem(STORAGE_TABS_KEY, JSON.stringify(tabs));
        },
        checkLoadRelated() {
            if (this.isOpen && this.$refs.relation) {
                this.$refs.relation.loadRelatedObjects();
            }
        },
        async loadInfoUsers() {
            this.isLoading = true;

            if (BEDITA.canReadUsers) {
                const creatorId = this.object?.meta?.created_by;
                const modifierId = this.object?.meta?.modified_by;
                const usersId = [creatorId, modifierId];
                const userRes = await fetch(`${API_URL}api/users?filter[id]=${usersId.join(',')}&fields[users]=name,surname,username`, API_OPTIONS);
                const userJson = await userRes.json();
                const users = userJson.data;

                users.map((user) => {
                    const href = `${BEDITA.base}/view/${user.id}`;
                    const userInfo = (user.attributes.name  != undefined || user.attributes.surname != undefined)
                        ? user.attributes.name + ' ' + user.attributes.surname
                        : user.attributes.username;

                    // using == because user.id String and creatorById Number
                    if(user.id == creatorId && userInfo!= undefined) {
                        document.querySelector(`td[name='created_by']`).innerHTML = `<a href="${href}">${userInfo}</a>`;
                    }

                    if (user.id == modifierId != undefined) {
                        document.querySelector(`td[name='modified_by']`).innerHTML = `<a href="${href}">${userInfo}</a>`;
                    }
                });
            }

            this.isLoading = false;
            this.userInfoLoaded = true;
        }
    }
}
