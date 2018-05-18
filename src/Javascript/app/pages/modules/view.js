/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

import PropertyView from 'app/components/property-view/property-view';
import RelationView from 'app/components/relation-view/relation-view';

export default {
    components: {
        PropertyView,
        RelationView,
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

    computed: {
        keyEvents() {
            return {
                'esc': {
                    keyup: this.toggleTabs,
                },
            }
        }
    },

    methods: {
        toggleTabs() {
            return this.tabsOpen = !this.tabsOpen;
        }
    }
}


