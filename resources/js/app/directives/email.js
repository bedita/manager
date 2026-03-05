import { createApp } from 'vue';

/**
 * Uri vue directive
 */
export default {
    install(app) {
        app.directive('email', {
            mounted(el) {
                import(/* webpackChunkName: "email-input" */'app/components/email-input')
                    .then(module => module.default)
                    .then((component) => {
                        const directiveApp = createApp(component, {
                            el,
                        });
                        directiveApp.mount(document.createElement('div'));
                        el.__emailDirectiveApp = directiveApp;
                    });
            },

            unmounted(el) {
                if (el.__emailDirectiveApp) {
                    el.__emailDirectiveApp.unmount();
                    delete el.__emailDirectiveApp;
                }
            },
        });
    }
}
