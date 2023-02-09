import { JSONEditor } from 'vanilla-jsoneditor';

const options = {
    mode: 'code',
    modes: ['tree', 'code'],
    history: true,
    search: true,
};

export default {
    template: `
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
        try {
            const element = this.el;
            let json = null;
            if (element.value !== 'null' && element.value.length  > 0) {
                json = JSON.parse(element.value);
            }
            element.style.display = 'none';
            const container = document.createElement('div');
            container.className = 'jsoneditor-container';
            element.parentElement.insertBefore(container, element);
            element.dataset.originalValue = element.value;
            const editorOptions = Object.assign(options, {
                content: {
                    json,
                },
                onChange: function () {
                    try {
                        const val = element.jsonEditor.get();
                        element.value = val.text === '' ? null : JSON.stringify(JSON.parse(val.text));
                        const isChanged = element.value !== element.dataset.originalValue;
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
        } catch (err) {
            console.error(err);
        }
    },
};
