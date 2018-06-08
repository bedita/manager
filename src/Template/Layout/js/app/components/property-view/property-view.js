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

export default {
    components: {
        RelationView,
    },

    props: {
        tabOpen: {
            type: Boolean,
            default: true,
                },
                isDefaultOpen: {
                    type: Boolean,
                    default: false,
                        },
                        },

                        data() {

                            return {
                                isOpen: true,
                                isLoading: false,
                                count: 0,
                            }
    },

    mounted() {
        this.isOpen = this.isDefaultOpen;
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
        onCount(n) {
            this.count = n;
        },
    }
}
