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

Vue.component('property-view', {
    template: `
        <div class="slide-container">
            <div @click.prevent="toggleVisibility()" class="tab"><h2><: label :></h2></div>

            <transition name="slide">
                <div v-if="isOpen" class="tab-container">
                    <slot></slot>
                </div>
            </transition>
        </div>`,

    props: {
        tabOpen: {
            type: Boolean,
            default: true,
        },
        label: {
            type: String,
            default: 'Properties',
        }
    },

    data() {
        return {
            isOpen: true,
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
        },
    }

});
