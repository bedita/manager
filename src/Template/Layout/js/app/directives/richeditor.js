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

/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */
export default {
    install(Vue) {
        Vue.directive('richeditor', {
            /**
             * dynamic load richtext-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            async inserted(el, binding) {
                const { ClassicEditor, ...plugins } = await import(/* webpackChunkName: "ckeditor" */'app/lib/ckeditor');

                let items = JSON.parse(binding.expression || '');
                if (!items) {
                    items = DEFAULT_TOOLBAR;
                } else if (!Array.isArray(items)) {
                    items = [items];
                }

                const editor = await ClassicEditor.create(el, {
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
                editor.model.schema.extend('$text', { allowAttributes });

                for (let i = 0; i < allowAttributes.length; i++) {
                    editor.conversion.attributeToAttribute({ model: allowAttributes[i], view: allowAttributes[i] });
                }

                let changing = false;

                el.addEventListener('change', () => {
                    if (!changing && el.value !== editor.getData()) {
                        editor.setData(el.value);
                    }
                });

                editor.model.document.on('change:data', () => {
                    let isChanged = el.value !== el.dataset.originalValue;

                    changing = true;
                    el.dispatchEvent(new CustomEvent('change', {
                        bubbles: true,
                        detail: {
                            id: el.id,
                            isChanged,
                        }
                    }));
                    changing = false;
                });
            },
        })
    }
}
