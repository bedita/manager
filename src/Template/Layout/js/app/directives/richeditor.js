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
                element.dataset.originalValue = element.value;
                editor.on('change', () => {
                    element.value = editor.getData();
                    let isChanged = element.value !== element.dataset.originalValue;
                    element.dispatchEvent(new CustomEvent('change', {
                        bubbles: true,
                        detail: {
                            id: element.id,
                            isChanged,
                        }
                    }));
                });
            },
        })
    }
}
