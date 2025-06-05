import tinymce from 'tinymce';
import { t } from 'ttag';

export const isSummary = function (node) {
    return node?.nodeName === 'SUMMARY';
}

export const isInSummary = function (editor) {
    const node = editor.selection.getNode();
    return isSummary(node) || Boolean(editor.dom.getParent(node, isSummary));
};

export const isInsertAllowed = function (editor) {
    return !isInSummary(editor) && !editor.mode.isReadOnly();
}

export const getSelectedDetails = function (editor) {
    return editor.dom.getParent(editor.selection.getNode(), 'details') ?? null;
};

export const isDetailsSelected = function (editor) {
    return !!getSelectedDetails(editor);
};

export function createDefaultSummary() {
    const summaryNode = tinymce.DOM.create('summary', {
        class: 'mce-accordion-summary',
    });
    summaryNode.textContent = t`Accordion title` + '...';
    return summaryNode;
}

export function insertAndSelectParagraphAfter (editor, target) {
    const paragraph = tinymce.DOM.create('p');
    paragraph.innerHTML = '<br data-mce-bogus="1" />';
    target.insertAdjacentElement('afterend', paragraph);
    editor.selection.setCursorLocation(paragraph, 0);
};

/** Add the default content to the accordion if the only child is a summary. */
export const normalizeContent = function (editor, accordion) {
    if (accordion?.lastChild?.nodeName === 'SUMMARY') {
        const paragraph = tinymce.DOM.create('p')
        paragraph.innerHTML = '<br data-mce-bogus="1" />';
        accordion.appendChild(paragraph);
        editor.selection.setCursorLocation(paragraph, 0);
    }
};

export const normalizeSummary = function (editor, accordion) {
    if (accordion?.firstChild?.nodeName !== 'SUMMARY') {
        var summary = createDefaultSummary();
        accordion.prepend(summary);
        editor.selection.setCursorLocation(summary, 0);
    }
};

export const normalizeAccordion = function (editor) {
    return function (accordion) {
        normalizeContent(editor, accordion);
        normalizeSummary(editor, accordion);
        // if the accordion is the last child of the body, insert a <br> after it so that the cursor can be placed after it
        if (editor.getBody().lastChild === accordion) {
            accordion.insertAdjacentElement('afterend', tinymce.DOM.create('br', { 'data-mce-bogus': '1'}));
        }
    };
};

export const normalizeDetails = function (editor) {
    tinymce.util.Tools.each(
        tinymce.util.Tools.grep(editor.dom.select('details', editor.getBody())),
        normalizeAccordion(editor)
    );
};
