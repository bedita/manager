/**
 * Datepicker vue directive
 */
export default {
    install(Vue) {
        Vue.directive('datepicker', {
            /**
             * create flatpicker instance when element is binded
             */
            inserted(el, binding, vnode) {
                const attrs = vnode.data && vnode.data.attrs;
                import(/* webpackChunkName: "date-input" */'app/components/date-input')
                    .then(module => module.default)
                    .then((component) => {
                        const Constructor = Vue.extend(component);
                        const vm = el.vm = new Constructor({
                            propsData: {
                                attrs,
                                el,
                            }
                        });
                        vm.$mount();
                    });
            },

            /**
             * update component value
             */
            componentUpdated(el, binding, vnode) {
                if (!el.vm) {
                    return;
                }
                if (JSON.stringify(el.vm.attrs) === JSON.stringify(vnode.data.attrs)) {
                    // no change in attributes, nothing to do
                    return;
                }
                el.vm.attrs = vnode.data.attrs;
                if (vnode.data && vnode.data.attrs && vnode.data.attrs.value) {
                    el.vm.setDate(new Date(vnode.data.attrs.value));
                } else if (vnode.data && vnode.data.domProps && vnode.data.domProps.value) {
                    el.vm.setDate(new Date(vnode.data.domProps.value));
                } else {
                    el.vm.setDate(null);
                }
            },

            /**
             * destroy instance
             */
            unbind(el) {
                if (el.vm) {
                    el.vm.$destroy();
                    delete el.vm;
                }
            },
        });
    }
}
