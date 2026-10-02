import { createApp } from 'vue';

/**
 *
 * v-jsoneditor directive to activate jsoneditor on element
 *
 */

export default {
    install(app) {
        app.directive('jsoneditor', {
            /**
             * dynamic load json-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            mounted(el) {
                import(/* webpackChunkName: "json-editor-input" */'app/components/json-editor-input')
                    .then(module => module.default)
                    .then((component) => {
                        const directiveApp = createApp(component, {
                            el,
                        });
                        directiveApp.mount(document.createElement('div'));
                        el.__jsonEditorDirectiveApp = directiveApp;
                    });
            },

            unmounted(el) {
                if (el.__jsonEditorDirectiveApp) {
                    el.__jsonEditorDirectiveApp.unmount();
                    delete el.__jsonEditorDirectiveApp;
                }
            },
        })
    }
}
