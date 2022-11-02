/**
 * Uri vue directive
 */
export default {
    install(Vue) {
        Vue.directive('email', {
            inserted (el) {
                import(/* webpackChunkName: "email-input" */'app/components/email-input')
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
