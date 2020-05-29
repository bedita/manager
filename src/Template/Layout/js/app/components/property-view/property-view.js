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
        ChildrenView: () => import(/* webpackChunkName: "children-view" */'app/components/children-view/children-view'),
        MapView: () => import(/* webpackChunkName: "map-view" */'app/components/map-view'),
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
    }
}
