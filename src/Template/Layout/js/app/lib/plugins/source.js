import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import CodeMirror from 'codemirror';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/lib/codemirror.css';

function format(html) {
    let tab = '\t';
    let result = '';
    let indent= '';

    html.split(/>\s*</).forEach((element) => {
        if (element.match( /^\/\w/ )) {
            indent = indent.substring(tab.length);
        }

        result += indent + '<' + element + '>\r\n';

        if (element.match( /^<?\w[^>]*[^\/]$/ )) {
            indent += tab;
        }
    });

    return result.substring(1, result.length-3);
}

export default class Source extends Plugin {
    init() {
        const editor = this.editor;
        editor.ui.componentFactory.add('editSource', (locale) => {
            let codemirror, wasReadOnly;
            const view = new ButtonView(locale);
            const textarea = document.createElement('textarea');

            view.set( {
                label: 'Source',
                withText: true,
            });

            view.on('execute', () => {
                if (!codemirror) {
                    wasReadOnly = editor.isReadOnly;
                    editor.isReadOnly = true;
                    editor.ui.element.appendChild(textarea);
                    textarea.value = format(editor.getData());
                    codemirror = CodeMirror.fromTextArea(textarea, {
                        mode: 'text/html',
                    });
                    codemirror.on('change', () => {
                        editor.setData(codemirror.getValue());
                    });
                } else {
                    codemirror.toTextArea();
                    editor.ui.element.removeChild(textarea);
                    editor.isReadOnly = wasReadOnly;
                    codemirror = null;
                }
            });

            return view;
        });
    }
}
