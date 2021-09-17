/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/roles.twig
 *
 * <roles-list-view> component used for ModulesPage -> View
 *
 * @inheritdoc
 *
 * @extends RelationshipsView
 *
 * @prop {Array} relatedObjects array of related objects
 *
 */


import RelationshipsView from 'app/components/relation-view/relationships-view/relationships-view';

export default {
    extends: RelationshipsView,

    data() {
        return {
            method: 'resources',
            removedRelations: [],
        }
    },

    props: {
        relatedObjects: {
            type: Array,
            default: () => [],
        },
    },

    /**
     * load content after component is mounted
     *
     * @return {void}
     */
    mounted() {
        this.loadObjects();
    },

    computed: {
        checkedRelations: {
            /**
             * getter of checkedRelations combines:
             * - relatedObjects
             * - pendingRelations
             * minus:
             * - removedRelations
             *
             * @returns {Array} all checked relations
             */
            get: function() {
                return this.relatedObjects.concat(this.pendingRelations).filter(rel => !this.containsId(this.removedRelations, rel.id));
            },

            /**
             * setter of checkedrelations:
             * add to pendingRelations newly added relation
             * add to removedRelations unchecked relation that are already in relatedObjects
             *
             * @emits Event#remove-relations params: relationsToRemove
             *
             * @returns {void}
            */
            set: function(newValue) {
                // handles relations to be added
                let relationsToAdd = newValue.filter((rel) => !this.containsId(this.relatedObjects, rel.id));
                this.pendingRelations = relationsToAdd;

                // handles relations to be removed
                let relationsToRemove = this.relatedObjects.filter((rel) => !this.containsId(newValue, rel.id));
                this.removedRelations = relationsToRemove;

                // emit event to pass data to parent
                this.$emit('remove-relations', relationsToRemove);
            }
        }
    },
}
