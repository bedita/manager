import CodeMirror from 'codemirror';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/hr';
import 'tinymce/plugins/visualblocks';
import '../plugins/tinymce/placeholders.js';
import '../plugins/tinymce/accordion/plugin.js';
import { tinymcePlugin } from '@chialab/typos';

tinymcePlugin(tinymce);

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
    'placeholders',
    'accordion',
    'link',
    'blockquote',
    'charmap',
    'hr',
    'table',
    '|',
    'undo',
    'redo',
    '|',
    'fixQuotes',
    'visualblocks',
    'code',
].join(' ');

import { EventBus } from 'app/components/event-bus';

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
                    if (BEDITA?.richeditorConfig?.cleanup_regex_pattern) {
                        const regex = new RegExp(
                            BEDITA.richeditorConfig.cleanup_regex_pattern,
                            BEDITA.richeditorConfig.cleanup_regex_argument || 'gs'
                        );
                        const content = event?.target?.editor?.getContent() || '';
                        const replacement = BEDITA.richeditorConfig.cleanup_regex_replacement || '';
                        const cleanContent = content.replace(regex, replacement);
                        if (cleanContent !== content) {
                            event.target.editor.setContent(cleanContent);
                        }
                    }
                    if (BEDITA?.richeditorConfig?.fields_regex_map?.[element?.name]) {
                        const { cleanup_regex_pattern, cleanup_regex_argument, cleanup_regex_replacement } = BEDITA.richeditorConfig.fields_regex_map[element.name];
                        const regex = new RegExp(cleanup_regex_pattern, cleanup_regex_argument || 'gs');
                        const content = event?.target?.editor?.getContent() || '';
                        const cleanContent = content.replace(regex, cleanup_regex_replacement || '');
                        if (cleanContent !== content) {
                            event.target.editor.setContent(cleanContent);
                        }
                    }
                });
            },

            unbind(element) {
                tinymce.remove(element.editor);
            },

            /**
             * dynamic load richtext-editor-input component and mount it
             *
             * @param {Object} element DOM object
             */
            async inserted(element, binding) {
                let elementName = element?.name || '';
                if (elementName.indexOf('fast-') === 0) {
                    const lastPos = elementName.lastIndexOf('-');
                    elementName = elementName.substring(lastPos + 1);
                }

                let changing = false;
                let toolbar = DEFAULT_TOOLBAR;
                if (binding?.value?.toolbar) {
                    toolbar = binding.value.toolbar.join(' ');
                } else if (binding?.expression) {
                    try {
                        const exp = JSON.parse(binding.expression);
                        toolbar = exp ? exp.join(' ') : toolbar;
                    } catch (e) {
                        console.log('Failed to parse toolbar expression:', e);
                        // do nothing
                    }
                }
                if (!binding.modifiers?.placeholders) {
                    toolbar = toolbar.replace(/\bplaceholders\b/, '');
                }
                const sizes = {};
                if (binding?.value?.config) {
                    const c = binding.value.config;
                    if (c?.height) {
                        sizes.height = c.height;
                    }
                    if (c?.min_height) {
                        sizes.min_height = c.min_height;
                    }
                }
                if (BEDITA?.richeditorByPropertyConfig?.[elementName]?.config?.height) {
                    sizes.height = BEDITA?.richeditorByPropertyConfig?.[elementName]?.config?.height;
                }
                sizes.min_height = BEDITA?.richeditorByPropertyConfig?.[elementName]?.config?.min_height || sizes.height || 300;
                if (BEDITA?.richeditorByPropertyConfig?.[elementName]?.config?.max_height) {
                    sizes.max_height = BEDITA?.richeditorByPropertyConfig?.[elementName]?.config?.max_height;
                }
                sizes.max_height = sizes.min_height > 500 ? sizes.min_height + 500 : 500;
                const { default: contentCSS } = await import('../../../richeditor.lazy.scss');
                const [editor] = await tinymce.init({
                    target: element,
                    skin: false,
                    content_css: contentCSS,
                    menubar: false,
                    branding: true,
                    ...sizes,
                    toolbar,
                    toolbar_mode: 'wrap',
                    block_formats: 'Paragraph=p; Header 1=h1; Header 2=h2; Header 3=h3',
                    entity_encoding: 'raw',
                    plugins: [
                        'paste',
                        'code',
                        'charmap',
                        'link',
                        'lists',
                        'table',
                        'hr',
                        'code',
                        'placeholders',
                        'accordion',
                        'typos',
                        'visualblocks',
                    ].join(' '),
                    extended_valid_elements: 'details,summary',
                    resize: true,
                    convert_urls: false,
                    relative_urls: false,
                    paste_block_drop: true,
                    add_unload_trigger: false, // fix populating textarea elements with garbage when the user initiates a navigation with unsaved changes, but cancels it when the alert is shown
                    readonly: element.getAttribute('readonly') === 'readonly' ? 1 : 0,
                    ... BEDITA?.richeditorConfig,
                    ... BEDITA?.richeditorByPropertyConfig?.[elementName]?.config || {},
                    setup: (editor) => {
                        editor.on('change', () => {
                            EventBus.send('refresh-placeholders', {id: editor.id, content: editor.getContent()});
                        });
                    }
                });

                element.editor = editor;
                element.addEventListener('change', () => {
                    if (!changing && element.value !== editor.getContent()) {
                        editor.setContent(element.value);
                    }
                });

                editor?.on('change', () => {
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

                EventBus.listen('replace-placeholder', (data) => {
                    if (editor.id !== data?.field) {
                        return;
                    }
                    const from = `<!--BE-PLACEHOLDER.${data.id}.${data.oldParams}-->`;
                    const to = `<!--BE-PLACEHOLDER.${data.id}.${data.newParams}-->`;
                    element.value = element.value.replace(from, to);
                    editor.setContent(element.value);
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
