const accordionTag = 'details';
const accordionDetailsClass = 'mce-accordion';
const accordionSummaryClass = 'mce-accordion-summary';
const accordionBodyWrapperClass = 'mce-accordion-body';
const accordionBodyWrapperTag = 'div';

const getClassList = function (node) {
    return node.attr('class')?.split(' ') ?? [];
};

const addClasses = function (node, classes) {
    const classListSet = new Set([...getClassList(node), ...classes]);
    const newClassList = Array.from(classListSet);

    if (newClassList.length > 0) {
        node.attr('class', newClassList.join(' '));
    }
};

const removeClasses = function (node, classes) {
    const newClassList = getClassList(node).filter((clazz) => !classes.has(clazz));
    node.attr('class', newClassList.length > 0 ? newClassList.join(' ') : null);
};

const isAccordionDetailsNode = function (node) {
    return node.name === accordionTag && getClassList(node).includes(accordionDetailsClass);
};

const isAccordionBodyWrapperNode = function (node) {
    return node.name === accordionBodyWrapperTag && getClassList(node).includes(accordionBodyWrapperClass);
};

const getAccordionChildren = function (accordionNode) {
    const children = accordionNode.children();
    let summaryNode;
    let wrapperNode;
    const otherNodes = [];

    for (let i = 0; i < children.length; i++) {
        const child = children[i];
        // Only want to get the first summary element
        if (child.name === 'summary' && !summaryNode) {
            summaryNode = child;
        } else if (isAccordionBodyWrapperNode(child) && !wrapperNode) {
            wrapperNode = child;
        } else {
            otherNodes.push(child);
        }
    }

    return { summaryNode, wrapperNode, otherNodes };
};

/**
 * Add br to node to ensure the cursor can be placed inside the node.
 * Mark as bogus so that it is converted to an nbsp on serialization.
 */
const padNode = function (node) {
    const br = tinymce.html.Node.create('br', { 'data-mce-bogus': '1' });
    node.empty();
    node.append(br);
};

const setup = function (editor) {
    editor.on('PreInit', function () {
        const { serializer, parser } = editor;

        // Purpose:
        // - add mce-accordion-summary class to summary node
        // - wrap details body in div and add mce-accordion-body class (TINY-9959 assists with Chrome selection issue)
        parser.addNodeFilter(accordionTag, function (nodes) {
            // Using a traditional for loop here as we may have to iterate over many nodes and it is the most performant way of doing so
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                if (isAccordionDetailsNode(node)) {
                    const accordionNode = node;
                    const { summaryNode, wrapperNode, otherNodes } = getAccordionChildren(accordionNode);
                    const newSummaryNode = summaryNode ?? tinymce.html.Node.create('summary');
                    // If there is nothing in the summary, pad it with a br
                    // so the cursor can be put inside the accordion summary
                    if (!newSummaryNode.firstChild) {
                        padNode(newSummaryNode);
                    }
                    addClasses(newSummaryNode, [accordionSummaryClass]);
                    if (!summaryNode) {
                        if (accordionNode.firstChild) {
                            accordionNode.insert(newSummaryNode, accordionNode.firstChild, true);
                        } else {
                            accordionNode.append(newSummaryNode);
                        }
                    }

                    const newWrapperNode = wrapperNode ?? tinymce.html.Node.create(accordionBodyWrapperTag);
                    newWrapperNode.attr('data-mce-bogus', '1');
                    addClasses(newWrapperNode, [accordionBodyWrapperClass]);
                    if (otherNodes.length > 0) {
                        for (let j = 0; j < otherNodes.length; j++) {
                            const otherNode = otherNodes[j];
                            newWrapperNode.append(otherNode);
                        }
                    }
                    // If there is nothing in the wrapper, append a placeholder p tag
                    // so the cursor can be put inside the accordion body
                    if (!newWrapperNode.firstChild) {
                        const pNode = tinymce.html.Node.create('p');
                        padNode(pNode);
                        newWrapperNode.append(pNode);
                    }
                    if (!wrapperNode) {
                        accordionNode.append(newWrapperNode);
                    }

                    if (editor.getBody().lastChild === accordionNode) {
                        accordionNode.insertAdjacentElement('afterend', tinymce.DOM.create('br', { 'data-mce-bogus': '1'}));
                    }
                }
            }
        });

        // Purpose:
        // - remove div wrapping details content as it is only required during editor (see TINY-9959 for details)
        // - remove mce-accordion-summary class on the summary node
        serializer.addNodeFilter(accordionTag, function (nodes) {
            const summaryClassRemoveSet = new Set([accordionSummaryClass]);
            // Using a traditional for loop here as we may have to iterate over many nodes and it is the most performant way of doing so
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                if (isAccordionDetailsNode(node)) {
                    const accordionNode = node;
                    const { summaryNode, wrapperNode } = getAccordionChildren(accordionNode);

                    if (summaryNode) {
                        removeClasses(summaryNode, summaryClassRemoveSet);
                    }

                    if (wrapperNode) {
                        wrapperNode.unwrap();
                    }
                }
            }
        });
    });
};

export { setup };
