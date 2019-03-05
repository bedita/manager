/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

import PropertyView from 'app/components/property-view/property-view';

export default {

    components: {
        PropertyView,
    },

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

    mounted() {
        window.addEventListener('keydown', this.toggleTabs);
    },

    methods: {
        toggleTabs(e) {
            let key = e.which || e.keyCode || 0;
            if(key === 27) {
                return this.tabsOpen = !this.tabsOpen;
            }
        },
    }
}
