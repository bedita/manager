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
                let loadedConfig = null;
                if (CkeditorConfig) {
                    loadedConfig = CkeditorConfig[configKey];
                }

                CKEDITOR.replace(element, loadedConfig);
            },
        })
    }
}
