/**
 * Datepicker vue directive
 *
 */


export default {
    install(Vue) {
        Vue.directive('datepicker', {
            /**
            * create flatpicker instance when element is binded
            *
            * @param {Object} element DOM object
            */
            inserted(el, binding, vnode) {
                const attrs = vnode.data && vnode.data.attrs;
                import(/* webpackChunkName: "date-input" */'app/components/date-input')
                    .then(module => module.default)
                    .then((component) => {
                        const Constructor = Vue.extend(component);
                        const vm = new Constructor({
                            propsData: {
                                attrs,
                                el,
                            }
                        });
                        vm.$mount();
                    });
            },
        })
    }
}
