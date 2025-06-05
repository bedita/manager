import 'tinymce/tinymce';
import tinymce from 'tinymce/tinymce';
import { t } from 'ttag';
import * as FilterContent from './filter-content.js';
import {
    createDefaultSummary,
    getSelectedDetails,
    insertAndSelectParagraphAfter,
    isInsertAllowed,
    normalizeDetails
} from './utils.js';

tinymce.util.Tools.resolve('tinymce.PluginManager').add(
    'accordion',
    function (editor) {
        const accordionHtml = [
            '<details class="mce-accordion">',
            tinymce.DOM.getOuterHTML(createDefaultSummary()),
            `<div class="mce-accordion-body"><p>${t`Accordion content`}...</p></div>`,
            '</details>',
        ].join('');

        editor.addCommand('RemoveAccordion', function () {
            if (!editor.mode.isReadOnly()) {
                const details = getSelectedDetails(editor);
                if (!details) {
                    return;
                }

                const { nextSibling } = details;
                if (nextSibling) {
                    editor.selection.select(nextSibling, true);
                    editor.selection.collapse(true);
                } else {
                    insertAndSelectParagraphAfter(editor, details);
                }

                details.remove();
            }
        });

        editor.ui.registry.addButton('accordion', {
            icon: 'toc',
            tooltip: 'Inserisci accordion',
            onAction() {
                if (!isInsertAllowed(editor)) {
                    return;
                }

                editor.undoManager.transact(function () {
                    editor.insertContent(accordionHtml);
                    const editorBody = editor.getBody();
                    const detailsList = editorBody.querySelectorAll('details');
                    detailsList.forEach(function (detailsElm) {
                        const summaryList = detailsElm.querySelectorAll('summary');
                        summaryList.forEach(function (summaryElm) {
                            // Set the cursor location to be at the end of the summary text
                            const rng = editor.dom.createRng();
                            rng.setStart(summaryElm, summaryElm.childNodes.length);
                            rng.setEnd(summaryElm, summaryElm.childNodes.length);
                            editor.selection.setRng(rng);
                        });
                    });
                });
            },
            onSetup(buttonApi) {
                // disable the button if insert is not allowed
                const onNodeChange = () => buttonApi.setDisabled(!isInsertAllowed(editor));
                editor.on('NodeChange', onNodeChange);
                return () => editor.off('NodeChange', onNodeChange);
            },
        });

        editor.ui.registry.addToggleButton('accordionremove', {
            icon: 'remove',
            tooltip: t`Remove accordion`,
            onAction() {
                editor.execCommand('RemoveAccordion');
            }
        });

        editor.ui.registry.addContextToolbar('accordion', {
            predicate(accordion) { return editor.dom.is(accordion, 'details') && editor.getBody().contains(accordion) },
            items: 'accordionremove',
            scope: 'node',
            position: 'node'
        });

        FilterContent.setup(editor);

        editor.on('NodeChange', function (e) {
            if (!e.parents?.some((node) => tinymce.DOM.is(node, 'details') || tinymce.DOM.is(node, 'summary'))) {
                return;
            }

            normalizeDetails(editor);
        });
    }
);
