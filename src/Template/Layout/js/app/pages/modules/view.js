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

        async translateAll(data, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');

            await Promise.all(
                Object.keys(data).map(key =>
                    this.fetchTranslation(data[key])
                )
            ).catch((error) => {
                    alert(error);
                    console.log(error);
                })
            .finally(() => {
                el.classList.remove('is-loading-spinner');
            });
        },

        translate(object, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');

            this.fetchTranslation(object)
                .catch((error) => {
                    alert(error);
                    console.log(error);
                })
                .finally(() => {
                    el.classList.remove('is-loading-spinner');
                });
        },

        async fetchTranslation(object) {
            await this.$helpers.autoTranslate(object.content, object.from, object.to)
                .catch(r => {
                    throw new Error(`Unablea to translate field ${object.field}`);
                })
                .then(r => {
                    if (!r.translation) {
                        throw new Error(`Unable to translate field ${object.field}`);
                    }

                    if (CKEDITOR.instances && CKEDITOR.instances['translated-fields-' + object.field]) {
                        CKEDITOR.instances['translated-fields-' + object.field].setData(r.translation);
                    } else {
                        this.$refs[object.field].value = r.translation;
                    }
                });
        },
    }
}
