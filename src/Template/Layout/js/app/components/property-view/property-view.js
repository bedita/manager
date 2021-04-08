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
    },

    data() {
        return {
            isOpen: this.isDefaultOpen,
            isLoading: false,
            totalObjects: 0,
            dataList: parseInt(this.uploadableNum) == 0,
        }
    },

    mounted() {
        if (this.tabOpenAtStart !== null) {
            this.isOpen = this.tabOpenAtStart;
            return;
        }
        this.isOpen = this.checkTabOpen();
        if (this.preCount >= 0) {
            this.totalObjects = this.preCount;
        }
    },

    watch: {
        tabOpen() {
            this.isOpen = this.tabOpen;
        },
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
            let tabs = [];
            if (localStorage.getItem(STORAGE_TABS_KEY)) {
                tabs = JSON.parse(localStorage.getItem(STORAGE_TABS_KEY));
            }
            return tabs;
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
    }
}
