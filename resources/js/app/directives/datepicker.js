import { createApp } from 'vue';

const getAttrs = (vnode) => {
    if (vnode?.props) {
        return vnode.props;
    }

    return vnode?.data?.attrs || {};
};

/**
 * Datepicker vue directive
 */
export default {
    install(app) {
        app.directive('datepicker', {
            /**
             * create flatpicker instance when element is binded
             */
            mounted(el, binding, vnode) {
                const attrs = getAttrs(vnode);
                import(/* webpackChunkName: "date-input" */'app/components/date-input')
                    .then(module => module.default)
                    .then((component) => {
                        const directiveApp = createApp(component, {
                            attrs,
                            el,
                        });
                        el.__datepickerDirectiveApp = directiveApp;
                        el.vm = directiveApp.mount(document.createElement('div'));
                    });
            },

            /**
             * update component value
             */
            updated(el, binding, vnode) {
                if (!el.vm) {
                    return;
                }
                const attrs = getAttrs(vnode);

                if (JSON.stringify(el.vm.attrs) === JSON.stringify(attrs)) {
                    // no change in attributes, nothing to do
                    return;
                }

                el.vm.attrs = attrs;
                if (attrs.value) {
                    el.vm.setDate(new Date(attrs.value));
                } else if (vnode?.data?.domProps?.value) {
                    el.vm.setDate(new Date(vnode.data.domProps.value));
                } else {
                    el.vm.setDate(null);
                }
            },

            /**
             * destroy instance
             */
            unmounted(el) {
                if (el.__datepickerDirectiveApp) {
                    el.__datepickerDirectiveApp.unmount();
                    delete el.__datepickerDirectiveApp;
                }

                if (el.vm) {
                    delete el.vm;
                }
            },
        });
    }
}
