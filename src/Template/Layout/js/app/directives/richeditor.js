/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */

import { CkeditorConfig } from 'config/config';
import { t } from 'ttag';

/**
 * add custom plugin to ckeditor that creates a command and a button for the toolbar
 */
CKEDITOR.plugins.add('addImageTagPlugin',
{
    init: function(editor)
    {
        editor.addCommand('addImgTag',
        {
            exec: function (edt) {
                let imgUrl = prompt(t`Please enter the image url`);
                if (imgUrl) {
                    edt.insertHtml(`<img src=${imgUrl}>`);
                }
            }
        });

        editor.ui.addButton('AddImageTag', {
            label: 'Click me',
            command: 'addImgTag',
            toolbar: 'insert',
            icon: 'Image'
        });
    }
});

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

                // load custom plugin and add button to toolbar
                loadedConfig.extraPlugins = 'addImageTagPlugin';
                loadedConfig.toolbar.push({ name: 'insert', groups: 'insert', items: ['AddImageTag'] });

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
