/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

export default {
    components: {
        PropertyView: () => import(/* webpackChunkName: "property-view" */'app/components/property-view/property-view'),
        HorizontalTabView: () => import(/* webpackChunkName: "horizontal-tab-view" */'app/components/horizontal-tab-view'),
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

        fetchAllTranslations() {
            this.$helpers.autoTranslate('<b>i will eat</b> an <span>aubergine</span>', 'en', 'it');
            this.$helpers.autoTranslate(['one', 'two', 'three'], 'en', 'it');
        },

        fetchTranslation(object) {
            this.$helpers.autoTranslate(object.content, object.from, object.to)
                .then((r) => {
                    if (r && r.translation) {
                        this.$refs[object.field].value = r.translation;
                    }
                    console.log('translate field:', object.field);
                    console.log('from:', object.from);
                    console.log('to:', object.to);
                    console.log(r);
                });
        },
    }
}
