/**
 * Uri vue directive
 */
export default {
    install(Vue) {
        Vue.directive('uri', {
            inserted (el) {
                import(/* webpackChunkName: "uri-input" */'app/components/uri-input')
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
        });
    }
}
