/**
 *  Extends RelationView: handles children relation
 *
 *  Templates that uses this component (directly or indirectly):
 *  Template/Elements/Form/children.twig
 *
 * <children-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName name of the relation used by the PaginatiedContentMixin
 *
 */

import Vue from 'vue';
import RelationView from 'app/components/relation-view/relation-view';

export default {
    extends: RelationView,

    data() {
        return {
            positions: {},
        }
    },

    methods: {
        /**
         * exectutes on sort objects in relation panel
         *
         * @param {Object}
         *
         * @returns {void}
         */
        onSort(transfer) {
            const list = Array.from(transfer.drop.children);
            const element = transfer.dragged;
            const newIndex = list.indexOf(element)
            const object = transfer.data;

            this.updatePositions(object, newIndex);
        },

        updatePositions(movedObject, newIndex) {
            const oldIndex = this.objects.findIndex((object) => movedObject.id === object.id);

            // moves the object in the objects array from the old index to the new index
            this.objects.splice(newIndex, 0, this.objects.splice(oldIndex, 1)[0]);

            this.objects = this.objects.map((object, index) => {
                object.meta.relation.position = index + 1;
                object.meta.relation.position += this.pagination.page_size * (this.pagination.page - 1);
                this.modifyRelation(object);
                return object;
            });
        },

        /**
         * update relation position and stage for saving
         *
         * @param {Object} related related object
         *
         * @returns {void}
         */
        onInputPosition(related) {
            const oldPosition = related.meta.relation.position;
            const newPosition = this.positions[related.id] !== '' ? this.positions[related.id] : undefined;
            if (newPosition !== oldPosition) {
                // try to deep copy the object
                try {
                    const copy = JSON.parse(JSON.stringify(related));
                    copy.meta.relation.position = newPosition;
                    this.modifyRelation(copy);
                } catch (exp) {
                    // silent error
                    console.error('[ChildrenView -> updatePosition] something\'s wrong with the data');
                }
            } else {
                this.removeModifiedRelations(related.id);
            }
        },
    }
}
