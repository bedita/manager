import { JSONEditor } from 'svelte-jsoneditor/dist/jsoneditor.js';

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
                    content: {
                        json,
                    },
                    onChange: function () {
                        try {
                            let val = element.jsonEditor.get();
                            val = JSON.parse(val.text);
                            element.value = JSON.stringify(val);

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
                element.jsonEditor = new JSONEditor({target: container, props: editorOptions});
            }
        } catch (err) {
            console.error(err);
        }
    },
};
