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
                        ]
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
