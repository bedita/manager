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
        tabOpenAtStart: {
            type: Boolean,
            default: false,
        },
        isDefaultOpen: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            isOpen: this.isDefaultOpen,
            isLoading: false,
            totalObjects: 0,
            listView: false,
        }
    },

    mounted() {
        this.isOpen = this.tabOpenAtStart;
    },

    watch: {
        tabOpen() {
            this.isOpen = this.tabOpen;
        },
    },

    methods: {
        toggleVisibility() {
            this.isOpen = !this.isOpen;
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
            this.listView = false;
        },
        switchListView() {
            this.listView = true;
        },
    }
}
