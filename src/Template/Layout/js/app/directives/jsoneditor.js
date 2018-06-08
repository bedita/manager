/**
 *
 * v-jsoneditor directive to activate jsoneditor on element
 *
 */

import JSONEditor from 'jsoneditor/dist/jsoneditor-minimalist';
import 'jsoneditor/dist/jsoneditor.min.css';

const options = {
    mode: 'code',
    modes: ['tree', 'code'],
    history: true,
    search: true,
};

export default {
    install(Vue) {
        Vue.directive('jsoneditor', {
            /**
             * create jsoneditor instance when element is inserted
             *
             * @param {Object} element DOM object
             */
            inserted(element) {
                const content = element.value;
                try {
                    const json = JSON.parse(content) || {};

                    if (json) {
                        element.style.display = "none";
                        let container = document.createElement('div');
                        container.className = 'jsoneditor-container';
                        element.parentElement.insertBefore(container, element);
                        let editorOptions = Object.assign(options, {
                            onChange: function () {
                                try {
                                    const json = element.jsonEditor.get();
                                    element.value = JSON.stringify(json);
                                    console.info('valid json :)');
                                } catch (e) {
                                    console.warn('still not valid json');
                                }
                            },
                        });
                        element.jsonEditor = new JSONEditor(container, editorOptions);
                        element.jsonEditor.set(json);
                    }
                } catch (err) {
                    console.error(err);
                }
            },
        })
    }
}
