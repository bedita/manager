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

import RelationView from 'app/components/relation-view/relation-view';
import ChildrenView from 'app/components/children-view/children-view';

export default {
    components: {
        RelationView,
        ChildrenView,
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
