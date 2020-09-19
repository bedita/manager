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
            async inserted(el) {
                const { ClassicEditor, ...plugins } = await import(/* webpackChunkName: "ckeditor" */'app/lib/ckeditor');

                const editor = await ClassicEditor.create(el, {
                    plugins: Object.values(plugins),
                    toolbar: {
                        shouldNotGroupWhenFull: true,
                        items: [
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
                            'link',
                            'bulletedList',
                            'numberedList',
                            'blockQuote',
                            'insertTable',
                            '|',
                            'undo',
                            'redo',
                        ],
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

                el.addEventListener('change', () => {
                    if (el.value !== editor.getData()) {
                        editor.setData(el.value);
                    }
                });

                editor.model.document.on('change:data', () => {
                    let isChanged = el.value !== element.dataset.originalValue;
                    el.dispatchEvent(new CustomEvent('change', {
                        bubbles: true,
                        detail: {
                            id: el.id,
                            isChanged,
                        }
                    }));
                });
            },
        })
    }
}
