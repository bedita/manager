import { createApp } from 'vue';

/**
 * Uri vue directive
 */
export default {
    install(app) {
        app.directive('uri', {
            mounted(el) {
                import(/* webpackChunkName: "uri-input" */'app/components/uri-input')
                    .then(module => module.default)
                    .then((component) => {
                        const directiveApp = createApp(component, {
                            el,
                        });
                        directiveApp.mount(document.createElement('div'));
                        el.__uriDirectiveApp = directiveApp;
                    });
            },

            unmounted(el) {
                if (el.__uriDirectiveApp) {
                    el.__uriDirectiveApp.unmount();
                    delete el.__uriDirectiveApp;
                }
            },
        });
    }
}
