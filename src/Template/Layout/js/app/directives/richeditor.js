import 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';

import 'tinymce/plugins/paste';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/hr';
import 'tinymce/plugins/code';

const DEFAULT_TOOLBAR = [
    'styleselect',
    '|',
    'bold',
    'italic',
    'underline',
    '|',
    'alignleft',
    'aligncenter',
    'alignright',
    '|',
    'charmap',
    'link',
    'bullist',
    'numlist',
    'blockquote',
    'table',
    'hr',
    '|',
    'undo',
    'redo',
    '|',
    'code',
].join(' ');

const emit = (vnode, name, data) => {
    let handlers = (vnode.data && vnode.data.on) || (vnode.componentOptions && vnode.componentOptions.listeners);
    if (handlers && handlers[name]) {
        handlers[name].fns(data);
    }
};

/**
 *
 * v-richeditor directive to activate tinymce on element
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
                let items = JSON.parse(binding.expression || '');
                if (!items) {
                    items = DEFAULT_TOOLBAR;
                }

                const [editor] = element.editor = await tinymce.init({
                    target: element,
                    skin: false,
                    content_css: false,
                    menubar: false,
                    branding: false,
                    toolbar: DEFAULT_TOOLBAR,
                    toolbar_mode: 'wrap',
                    block_formats: 'Paragraph=p; Header 1=h1; Header 2=h2; Header 3=h3',
                    plugins: [
                        'paste',
                        'code',
                        'charmap',
                        'link',
                        'lists',
                        'table',
                        'hr',
                        'code',
                    ].join(' '),
                });

                console.log(editor);

                let changing = false;

                element.addEventListener('change', () => {
                    if (!changing && element.value !== editor.getContent()) {
                        editor.setContent(element.value);
                    }
                });

                editor.on('change', () => {
                    let isChanged = element.value !== element.dataset.originalValue;

                    changing = true;
                    element.value = editor.getContent();
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

                if (element.value !== editor.getContent()) {
                    editor.setContent(element.value);
                }
            },
        })
    }
}
