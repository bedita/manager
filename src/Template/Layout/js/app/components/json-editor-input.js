import JSONEditor from 'jsoneditor/dist/jsoneditor-minimalist';

import 'jsoneditor/dist/jsoneditor.min.css';

const options = {
    mode: 'code',
    modes: ['tree', 'code'],
    history: true,
    search: true,
};

export default {
    template: /* template */`
    <div>
        <slot></slot>
    </div>
    `,

    props: {
        el: {
            type: HTMLTextAreaElement,
        },
    },

    async mounted() {
        const element = this.el;
        const content = element.value;
        try {
            const json = content !== "" && JSON.parse(content) || {};

            if (json) {
                element.style.display = "none";
                let container = document.createElement('div');
                container.className = 'jsoneditor-container';
                element.parentElement.insertBefore(container, element);
                element.dataset.originalValue = element.value;
                let editorOptions = Object.assign(options, {
                    onChange: function () {
                        try {
                            const json = element.jsonEditor.get();
                            element.value = JSON.stringify(json);
                            console.info('valid json :)');

                            let isChanged = element.value !== element.dataset.originalValue;
                            element.dispatchEvent(new CustomEvent('change', {
                                bubbles: true,
                                detail: {
                                    id: element.id,
                                    isChanged,
                                }
                            }));
                        } catch(e) {
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
};
