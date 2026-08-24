import { JSONEditor } from 'vanilla-jsoneditor';

const options = {
    mode: 'text',
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
        const element = this.el;
        let json = null;
        try {
            if (element?.value !== 'null' && element?.value.length  > 0) {
                json = JSON.parse(element.value);
            }
        } catch (error) {
            console.error(error);
        }

        try {
            element.style.display = 'none';
            const container = document.createElement('div');
            container.className = 'jsoneditor-container';
            element.parentElement.insertBefore(container, element);
            element.dataset.originalValue = element?.value;
            const editorOptions = Object.assign(options, {
                content: {
                    json,
                },
                readOnly: element.getAttribute('readonly') === 'readonly' ? true : false,
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
                        console.warn('still not valid json', e);
                    }
                },
            });
            element.jsonEditor = new JSONEditor({target: container, props: editorOptions});
        } catch (err) {
            console.error(err);
        }
    },
};
