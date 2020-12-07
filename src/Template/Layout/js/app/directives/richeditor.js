const DEFAULT_TOOLBAR = [
    'heading',
    '|',
    'bold',
    'italic',
    'underline',
    'strikethrough',
    'code',
    'subscript',
    'superscript',
    'removeFormat',
    '|',
    'alignment',
    '|',
    'specialCharacters',
    'link',
    'bulletedList',
    'numberedList',
    'blockQuote',
    'insertTable',
    'horizontalLine',
    '|',
    'undo',
    'redo',
    '|',
    'editSource',
];

const emit = (vnode, name, data) => {
    let handlers = (vnode.data && vnode.data.on) || (vnode.componentOptions && vnode.componentOptions.listeners);
    if (handlers && handlers[name]) {
        handlers[name].fns(data);
    }
};

/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */
export default {
    install(Vue) {
        Vue.directive('richeditor', {
            bind(element, binding, vnode) {
                element.addEventListener('change', (event) => {
                    emit(vnode, 'input', event);
                });
            },

            /**
             * dynamic load richtext-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            async inserted(element, binding, vnode) {
                const { ClassicEditor, ...plugins } = await import(/* webpackChunkName: "ckeditor" */'app/lib/ckeditor');

                let items = JSON.parse(binding.expression || '');
                if (!items) {
                    items = DEFAULT_TOOLBAR;
                } else if (!Array.isArray(items)) {
                    items = [items];
                }

                const editor = element.editor = await ClassicEditor.create(element, {
                    plugins: Object.values(plugins),
                    toolbar: {
                        shouldNotGroupWhenFull: true,
                        items,
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                            { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' },
                        ],
                    },
                });

                const allowAttributes = [
                    'id',
                    'name',
                    'class',
                    'style',
                    'lang',
                    'role',
                    'aria-label',
                ];

                editor.model.schema.extend('$root', { allowAttributes } );
                editor.model.schema.extend('$block', { allowAttributes } );

                for (let i = 0; i < allowAttributes.length; i++) {
                    editor.conversion.attributeToAttribute({ model: allowAttributes[i], view: allowAttributes[i] });
                }

                let changing = false;

                element.addEventListener('change', () => {
                    if (!changing && element.value !== editor.getData()) {
                        editor.setData(element.value);
                    }
                });

                editor.model.document.on('change:data', () => {
                    let isChanged = element.value !== element.dataset.originalValue;

                    changing = true;
                    element.value = editor.getData();
                    element.dispatchEvent(new CustomEvent('change', {
                        bubbles: true,
                        detail: {
                            id: element.id,
                            isChanged,
                        },
                    }));
                    changing = false;
                });
            },

            update(element) {
                const editor = element.editor;
                if (!editor) {
                    return;
                }

                if (element.value !== editor.getData()) {
                    editor.setData(element.value);
                }
            },
        })
    }
}
