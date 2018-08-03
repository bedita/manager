/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/panel.twig
 *
 * <panel> component used for Panel
 *
 */

import RelationsAdd from 'app/components/relation-view/relations-add';


export default {
    components: {
        RelationsAdd,
    },
    props: {
        panelIsOpen: {
            type: Boolean,
            default: false,
        },
        addRelation: {
            type: Object,
            default: () => ({
                name: '',
                alreadyInView: [],
            }),
        },
    },
    data() {
        return {
            draggableOptions: {
                dropzoneSelector: '[droppable]',
                draggableSelector: '[draggable]',
                excludeOlderBrowsers: true,
                multipleDropzonesItemsDraggingEnabled: true,
                showDropzoneAreas: true,
                onDrop: function(event) {
                    console.log('drop edo', event);
                },
                onDragstart: function(event) {
                    console.log('edo');
                },
                onDragend: function(event) {}
            },
        };
    },
}
