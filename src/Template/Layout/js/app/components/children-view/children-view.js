/**
 *  Extends RelationView: handles children relation
 *
 *  Templates that uses this component (directly or indirectly):
 *  Template/Elements/Form/children.twig
 *
 * <children-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName name of the relation used by the PaginatiedContentMixin
 * @prop {Boolean} loadOnStart load content on component init
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
         * update relation position and stage for saving
         *
         * @param {Object} related
         *
         * @returns {void}
         */
        updatePosition(related) {
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
