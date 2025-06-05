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

export function createDefaultSummary() {
    const summaryNode = tinymce.DOM.create('summary', {
        class: 'mce-accordion-summary',
    });
    summaryNode.textContent = t`Accordion title` + '...';
    return summaryNode;
}

export function createDefaultBody() {
    const accordionBodyNode = tinymce.DOM.create('div', {
        class: 'mce-accordion-body',
    });
    accordionBodyNode.textContent = t`Accordion content` + '...';
    return accordionBodyNode;
}

export function insertAndSelectParagraphAfter (editor, target) {
    const paragraph = tinymce.DOM.create('p');
    paragraph.innerHTML = '<br data-mce-bogus="1" />';
    target.insertAdjacentElement('afterend', paragraph);
    editor.selection.setCursorLocation(paragraph, 0);
};

/** Add the deafult content to the accordion if the only child is a summary. */
export const normalizeContent = function (editor, accordion) {
    if (accordion?.lastChild?.nodeName === 'SUMMARY') {
        var bodyNode = createDefaultBody();
        accordion.appendChild(bodyNode);
        editor.selection.setCursorLocation(bodyNode, 0);
    }
};

export const normalizeSummary = function (editor, accordion) {
    if (accordion?.firstChild.nodeName !== 'SUMMARY') {
        var summary = createDefaultSummary();
        accordion.prepend(summary);
        editor.selection.setCursorLocation(summary, 0);
    }
};

export const normalizeAccordion = function (editor) {
    return function (accordion) {
        normalizeContent(editor, accordion);
        normalizeSummary(editor, accordion);
    };
};

export const normalizeDetails = function (editor) {
    tinymce.util.Tools.each(
        tinymce.util.Tools.grep(editor.dom.select('details', editor.getBody())),
        normalizeAccordion(editor)
    );
};

export const getSelectedDetails = function (editor) {
    return editor.dom.getParent(editor.selection.getNode(), 'details') ?? null;
};

export const isDetailsSelected = function (editor) {
    return !!getSelectedDetails(editor);
};
