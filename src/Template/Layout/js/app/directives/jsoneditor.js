/**
 *
 * v-jsoneditor directive to activate jsoneditor on element
 *
 */

export default {
    install(Vue) {
        Vue.directive('jsoneditor', {
            /**
             * dynamic load json-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            inserted (el) {
                import(/* webpackChunkName: "json-editor-input" */'app/components/json-editor-input')
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
