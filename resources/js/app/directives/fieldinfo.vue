<script>
export default {
    install(Vue) {
        Vue.directive('fieldinfo', {
            inserted (el, binding, vnode) {
                const attrs = vnode.data && vnode.data.attrs;
                console.log(vnode);
                console.log(attrs);
                import(/* webpackChunkName: "field-info" */'app/components/field-info')
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
        });
    }
}
</script>
