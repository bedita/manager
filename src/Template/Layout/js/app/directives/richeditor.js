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

                ClassicEditor.create(el, {
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
            },
        })
    }
}
