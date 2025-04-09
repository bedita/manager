import { t } from 'ttag';
import 'tinymce/tinymce';
import { EventBus } from 'app/components/event-bus';
import { PanelEvents } from 'app/components/panel-view';
import tinymce from 'tinymce/tinymce';
import { utf8ToBase64, base64ToUtf8 } from 'app/helpers/text-helper';

const cache = {};
const regex = /BE-PLACEHOLDER\.(\d+)(?:\.([a-zA-Z0-9+/]+={0,2}))?/;
const baseUrl = new URL(BEDITA.base).pathname;
const options = {
    credentials: 'same-origin',
    headers: {
        accept: 'application/json',
    }
};

function fetchData(id) {
    if (!cache[id]) {
        cache[id] = fetch(`${baseUrl}resources/get/${id}`, options)
            .then((response) => response.json());
    }

    return cache[id];
}

function loadPreview(editor, node, id) {
    node.empty();

    let text = tinymce.html.Node.create('#text');
    text.value = 'loading…';
    node.append(text);

    fetchData(id)
        .then((json) => json.data)
        .then((data) => {
            if (!data) {
                return;
            }
            let domElements = editor.getBody().querySelectorAll(`[data-placeholder="${data.id}"]`);
            [...domElements].forEach((dom) => {
                let content = '';
                switch (data.type) {
                    case 'images':
                        content = `<img src="${data.meta.media_url}" alt="${data.attributes.title}" />`;
                        break;
                    default:
                        if (data.meta?.media_url) {
                            content = `<iframe src="${data.meta.media_url}" sandbox=""></iframe>`;
                        } else {
                            content = `<div class="embed-card">
                                <div class="title">${data.attributes.title || t('Untitled')}</div>
                                <div class="description">${data.attributes.description || ''}</div>
                            </div>`
                        }
                        break;
                }
                dom.innerHTML = content;
            });
        });
}

tinymce.util.Tools.resolve('tinymce.PluginManager').add('placeholders', function(editor) {
    editor.on('PreInit', function () {
        editor.parser.addAttributeFilter('data-placeholder', function(nodes) {
            nodes.forEach((node) => {
                let comment = node.firstChild.value;
                if (!comment) {
                    return;
                }
                let match = comment.match(regex);
                if (!match) {
                    return;
                }
                let [, id, params] = match;
                node.attr('data-params', params ? base64ToUtf8(params) : '');
                node.attr('contenteditable', 'false');
                loadPreview(editor, node, id);
            });
        });
        editor.serializer.addAttributeFilter('data-placeholder', function(nodes) {
            nodes.forEach((node) => {
                let id = node.attributes.map['data-placeholder'];
                let params = node.attributes.map['data-params'];
                node.empty();
                let comment = tinymce.html.Node.create('#comment');
                comment.value = `BE-PLACEHOLDER.${id}.${utf8ToBase64(params)}`;
                node.append(comment);
            });
        });
    });

    editor.ui.registry.addButton('placeholders', {
        icon: 'image',
        tooltip: t`Add placeholder`,
        onAction() {
            PanelEvents.requestPanel({
                action: 'relations-add',
                from: this,
                data: {
                    relationName: 'placeholder',
                    relationLabel: 'Placeholder',
                    relationTypes: {right: BEDITA?.relationsSchema?.placeholder?.right || null},
                },
            });

            let onSave = (objects) => {
                PanelEvents.closePanel();
                PanelEvents.stop('relations-add:save', this, onSave);
                PanelEvents.stop('panel:closed', null, onClose);
                objects.forEach((data) => {
                    let node = editor.selection.getNode();
                    let isEmptyBlock = node && node.children.length === 1 && node.children[0].tagName === 'BR';
                    let view = editor.dom.create(isEmptyBlock ? 'div' : 'span', {
                        'data-placeholder': data.id,
                        'data-params': data.params,
                    }, `<!-- BE-PLACEHOLDER.${data.id}.${utf8ToBase64(data.params)} -->`);
                    editor.insertContent(view.outerHTML);
                    if (isEmptyBlock) {
                        tinymce.dom.DOMUtils.DOM.remove(node);
                    }
                });
                EventBus.send('refresh-placeholders', {id: editor.id, content: editor.getContent()});
            };

            let onClose = () => {
                PanelEvents.stop('relations-add:save', this, onSave);
                PanelEvents.stop('panel:closed', null, onClose);
            };

            PanelEvents.listen('relations-add:save', this, onSave);
            PanelEvents.listen('panel:closed', null, onClose);
        },
    });
});
