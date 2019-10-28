/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */

export default {
    install(Vue) {
        Vue.directive('richeditor', {
            /**
             * dynamic load richtext-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            inserted (el) {
                import(/* webpackChunkName: "richtext-editor-input" */'app/components/richtext-editor-input')
                    .then(module => module.default)
                    .then((component) => {
                        const Constructor = Vue.extend(component);
                        const vm = new Constructor({
                            propsData: {
                                el,
                            }
                        });
                        vm.$mount();
                    });
            },
        })
    }
}
