/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */

import { CkeditorConfig } from 'config/config';

export default {
    install(Vue) {
        Vue.directive('richeditor', {
            /**
             * When the bound element is inserted into the init CKeditor
             *
             * @param {Object} element DOM object
             */
            inserted (element) {
                const configKey = element.getAttribute('ckconfig');
                let loadedConfig = {};
                if (CkeditorConfig) {
                    loadedConfig = CkeditorConfig[configKey];
                }

                let editor = CKEDITOR.replace(element, loadedConfig);
                editor.on('change', () => {
                    element.value = editor.getData();
                    element.dispatchEvent(new Event('change'));
                    let form = element.closest('form');
                    if (form) {
                        form.dispatchEvent(new Event('change'));
                    }
                });
            },
        })
    }
}
