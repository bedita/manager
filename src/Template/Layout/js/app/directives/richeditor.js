import CodeMirror from 'codemirror';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/hr';

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
    'bullist',
    'numlist',
    '|',
    'link',
    'blockquote',
    'charmap',
    'hr',
    'table',
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

            unbind() {
                tinymce.remove();
            },

            /**
             * dynamic load richtext-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            async inserted(element, binding) {
                let changing = false;
                let items = JSON.parse(binding.expression || '');
                if (!items) {
                    items = DEFAULT_TOOLBAR;
                }

                const { default: contentCSS } = await import('../../../richeditor.lazy.scss');
                const [editor] = await tinymce.init({
                    target: element,
                    skin: false,
                    content_css: contentCSS,
                    menubar: false,
                    branding: false,
                    max_height: 500,
                    toolbar: DEFAULT_TOOLBAR,
                    toolbar_mode: 'wrap',
                    block_formats: 'Paragraph=p; Header 1=h1; Header 2=h2; Header 3=h3',
                    entity_encoding: 'raw',
                    plugins: [
                        'paste',
                        'autoresize',
                        'code',
                        'charmap',
                        'link',
                        'lists',
                        'table',
                        'hr',
                        'code',
                    ].join(' '),
                    autoresize_bottom_margin: 50,
                    relative_urls : false,
                });

                element.editor = editor;
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

function format(html) {
    let tab = '\t';
    let result = '';
    let indent= '';

    html.split(/>\s*</).forEach((element) => {
        if (element.match( /^\/\w/ )) {
            indent = indent.substring(tab.length);
        }

        result += indent + '<' + element + '>\r\n';

        if (element.match( /^<?\w[^>]*[^/]$/ )) {
            indent += tab;
        }
    });

    return result.substring(1, result.length-3);
}

const setContent = (editor, html) => {
    editor.focus();
    editor.undoManager.transact(function () {
        editor.setContent(html);
    });
    editor.selection.setCursorLocation();
    editor.nodeChanged();
};

const getContent = (editor) => editor.getContent({ source_view: true });

const open = async (editor) => {
    const editorContent = getContent(editor);
    editor.windowManager.open({
        title: 'Source Code',
        size: 'large',
        body: {
            type: 'panel',
            items: [{
                type: 'textarea',
                name: 'code',
                id: 'codemirror',
            }],
        },
        buttons: [
            {
                type: 'cancel',
                name: 'cancel',
                text: 'Cancel'
            },
            {
                type: 'submit',
                name: 'save',
                text: 'Save',
                primary: true,
            }
        ],
        initialData: { code: editorContent },
        onSubmit: (api) => {
            setContent(editor, api.getData().code);
            api.close();
        },
    });

    let textarea = document.querySelector('.tox-textarea');
    textarea.value = format(editor.getContent());
    let codemirror = CodeMirror.fromTextArea(textarea, {
        mode: 'text/html',
        lineNumbers: true,
        theme: 'monokai',
    });

    codemirror.on('change', () => {
        textarea.value = codemirror.getValue();
    });
};

const register = (editor) => {
    editor.addCommand('mceCodeEditor', () => {
        open(editor);
    });

    editor.ui.registry.addButton('code', {
        icon: 'sourcecode',
        tooltip: 'Source code',
        onAction: () => open(editor),
    });

    editor.ui.registry.addMenuItem('code', {
        icon: 'sourcecode',
        text: 'Source code',
        onAction: () => open(editor),
    });
};

tinymce.util.Tools.resolve('tinymce.PluginManager').add('code', function(editor) {
    register(editor);
    return {};
});
