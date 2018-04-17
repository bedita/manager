/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relation-view> component used for ModulesPage -> View
 *
 */

Vue.component('property-view', {
    template: `
        <div class="slide-container">
            <div @click.prevent="toggleVisibility()" class="tab"><h2><% label %></h2></div>

            <transition name="slide">
                <div v-if="isOpen" class="tab-container">
                    <slot></slot>
                </div>
            </transition>
        </div>`,

    props: ['tabOpen', 'label'],

    data() {
        return {
            isOpen: true,
        }
    },

    // computed: {
    //     isOpen() {
    //         return this.visibility || !this.visibility && this.tabsOpen
    //     }
    // },

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
