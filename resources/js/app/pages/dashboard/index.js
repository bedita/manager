/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Pages/Dashboard/index.twig
 *
 * <dashboard> component used for Dashboard -> Index
 *
 */

export default {

    components: {
        IconConcept: () => import(/* webpackChunkName: "icon-concept" */'@carbon/icons-vue/es/concept/32.js'),
        IconDownload: () => import(/* webpackChunkName: "icon-download" */'@carbon/icons-vue/es/download/32.js'),
        IconGrid: () => import(/* webpackChunkName: "icon-grid" */'@carbon/icons-vue/es/grid/32.js'),
        IconLogin: () => import(/* webpackChunkName: "icon-login" */'@carbon/icons-vue/es/login/16.js'),
        IconSearch: () => import(/* webpackChunkName: "icon-search" */'@carbon/icons-vue/es/search/16.js'),
        IconSettings: () => import(/* webpackChunkName: "icon-settings" */'@carbon/icons-vue/es/settings/32.js'),
        IconTrashCan: () => import(/* webpackChunkName: "icon-trash-can" */'@carbon/icons-vue/es/trash-can/32.js'),
        IconTreeView: () => import(/* webpackChunkName: "icon-tree-view" */'@carbon/icons-vue/es/tree-view/32.js'),
        IconUserAdmin: () => import(/* webpackChunkName: "icon-user-admin" */'@carbon/icons-vue/es/user--admin/32.js'),
        IconUserProfile: () => import(/* webpackChunkName: "icon-user-profile" */'@carbon/icons-vue/es/user--profile/32.js'),
        IconWikis: () => import(/* webpackChunkName: "icon-wikis" */'@carbon/icons-vue/es/wikis/32.js'),
    },

    props: {
        q: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            searchString: '',
        };
    },
    created() {
        if (document.referrer.endsWith('/login') && window.top !== window) {
            window.top.postMessage('login', BEDITA.base);
        }

        this.searchString = this.q;
    },
    methods: {
        captureKeys(e) {
            let key = e.which || e.keyCode || 0;
            switch (key) {
                case 13:
                    this.searchObjects();
                    break;
                case 27:
                    this.popUpAction = '';
                    break;
            }
        },

        searchObjects() {
            if (this.searchString) {
                this.$refs.searchSubmit.classList.add('is-loading-spinner');
                window.location.href = BEDITA.base + '/objects?q=' + this.searchString;
            }
        },
    }
}
