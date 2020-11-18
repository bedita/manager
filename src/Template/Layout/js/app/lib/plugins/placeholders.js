import { PanelEvents } from 'app/components/panel-view';
import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import imageIcon from '@ckeditor/ckeditor5-core/theme/icons/image.svg';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';

export default class InsertPlaceholders extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('insertPlaceholder', (locale) => {
            const view = new ButtonView(locale);
            const schema = this.editor.model.schema;
            const conversion = this.editor.conversion;

            // add the button view to the editor
            view.set({
                label: 'Insert placeholder',
                icon: imageIcon,
                tooltip: true
            });

            // add placeholder relation on button click
            view.on('execute', () => {
                PanelEvents.requestPanel({
                    action: 'relations-add',
                    from: this,
                    data: {
                        relationName: 'placeholder',
                    },
                });

                // @TODO get list

                editor.model.change((writer) => {
                    let data = {};
                    let element = writer.createElement('placeholder', {
                        id: data.id,
                        type: data.type,
                        params: {},
                        data,
                    });

                    editor.model.insertContent(element, editor.model.document.selection);
                    console.log(editor.model);
                });
            });

            // register the placeholder schema to the ckeditor model
            schema.register('placeholder', {
                // The placeholder will act as an inline node:
                isInline: true,

                // The inline widget is self-contained so it cannot be split by the caret and it can be selected:
                isObject: true,
            });

            // convert the placeholder model to ckeditor output
            conversion.for('upcast').elementToElement( {
                view: {
                    name: 'div',
                    classes: 'bedita-placeholder',
                },
                model: (viewElement, { writer }) => {
                    console.log('VIEW ELEMENT', viewElement);
                    let fragment = upcastWriter.createDocumentFragment(viewElement.getChildren());
                    let comment = htmlProcessor.toData(fragment);
                    console.log('DATA', viewElement);

                    return writer.createElement('placeholder', {
                        id: comment,
                        type: comment,
                        params: comment,
                    });
                }
            });

            // convert html to ckeditor placeholder model
            conversion.for('dataDowncast').elementToElement( {
                model: 'placeholder',
                view: (modelElement, { writer }) => writer.createRawElement('div', { class: 'bedita-placeholder' }, (domElement) => {
                    domElement.innerHTML = `<!-- BEPLACEHOLDER ${domElement.dataset.id} -->`;
                }),
            });

            return view;
        });
    }
}
