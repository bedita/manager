/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

Vue.component('modules-view', {

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            tabsOpen: true,
        };
    },

    computed: {
        keyEvents() {
            return {
                'esc': {
                    keydown: this.toggleTabs,
                    keyup: this.toggleTabs,
                }
            }
        }
    },

    methods: {
        toggleTabs() {
            console.log('toggle');
            return this.tabsOpen = !this.tabsOpen;
        }
    }
});


