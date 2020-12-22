import 'tinymce/tinymce';
import { PanelEvents } from 'app/components/panel-view';
import tinymce from 'tinymce/tinymce';

/**
 * @todo
 * custom element
 * size
 * resize/floating
 * draggable
 * not editable content
 */

const cache = {};
const regex = /BE-PLACEHOLDER\.(\d+)\.([-A-Za-z0-9+=]{1,50}|=[^=]|={3,})/;
const baseUrl = new URL(BEDITA.base).pathname;
const options = {
    credentials: 'same-origin',
    headers: {
        accept: 'application/json',
    }
};

function fetchData(id) {
    if (!cache[id]) {
        let fetchType = fetch(`${baseUrl}api/objects/${id}`, options)
            .then((response) => response.json())
            .then((json) => json.data.type);
        cache[id] = fetchType
            .then((type) => fetch(`${baseUrl}api/${type}/${id}`, options))
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

            let dom = editor.getBody().querySelector(`[data-placeholder="${data.id}"]`);
            if (!dom) {
                return;
            }

            let content = '';
            switch (data.type) {
                case 'images':
                    content = `<img src="${data.meta.media_url}" alt="${data.attributes.title}" />`;
                    break;
                default:
                    if (data.meta.media_url) {
                        content = `<iframe src="${data.meta.media_url}"></iframe>`;
                    } else {
                        content = `<div class="embed-card">
                            <h1>${data.attributes.title || t('Untitled')}</h1>
                            <div class="description">${data.attributes.description || ''}</div>
                        </div>`
                    }
                    break;
            }

            dom.innerHTML = content;
        });
}

tinymce.util.Tools.resolve('tinymce.PluginManager').add('placeholders', function(editor) {
    editor.on('PreInit', function () {
        editor.parser.addAttributeFilter('data-placeholder', function(nodes) {
            nodes.forEach((node) => {
                let comment = node.firstChild.value;
                let match = comment.match(regex);
                if (!match) {
                    return;
                }
                let [, id, params] = match;
                node.attr('style', params || '');
                node.attr('contenteditable', 'false');
                loadPreview(editor, node, id);
            });
        });
        editor.serializer.addAttributeFilter('data-placeholder', function(nodes) {
            nodes.forEach((node) => {
                let id = node.attributes.map['data-placeholder'];
                let params = node.attributes.map['style'];
                node.empty();
                let comment = tinymce.html.Node.create('#comment');
                comment.value = `BE-PLACEHOLDER.${id}.${btoa(params)}`;
                node.append(comment);
            });
        });
    });

    editor.ui.registry.addButton('placeholders', {
        icon: 'image',
        tooltip: 'Add placeholder',
        onAction() {
            PanelEvents.requestPanel({
                action: 'relations-add',
                from: this,
                data: {
                    relationName: 'placeholders',
                },
            });

            let onSave = (objects) => {
                PanelEvents.closePanel();
                PanelEvents.stop('relations-add:save', this, onSave);
                PanelEvents.stop('panel:close', this, onClose);
                objects.forEach((data) => {
                    let view = editor.dom.create('span', {
                        'data-placeholder': data.id,
                        'style': data.params,
                    }, `<!-- BE-PLACEHOLDER.${data.id}.${btoa(data.params)} -->`);
                    editor.insertContent(view.outerHTML);
                });
            };

            let onClose = () => {
                PanelEvents.stop('relations-add:save', this, onSave);
                PanelEvents.stop('panel:close', this, onClose);
            };

            PanelEvents.listen('relations-add:save', this, onSave);
            PanelEvents.listen('panel:close', this, onClose);
        },
    });
});
