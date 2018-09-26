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
            method: 'resourcesJson',
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
                let allrelations = this.relatedObjects.concat(this.pendingRelations).filter(rel => {
                    return !this.isRemoved(rel.id)}
                );
                return allrelations;
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
                let relationsToAdd = newValue.filter((rel) => {
                    return !this.isRelated(rel.id);
                });

                this.pendingRelations = relationsToAdd;

                // handles relations to be removed
                let relationsToRemove = this.relatedObjects.filter((rel) => {
                    return !this.containsId(newValue, rel.id);
                });

                this.removedRelations = relationsToRemove;

                // emit event to pass data to parent
                this.$emit('remove-relations', relationsToRemove);
            }
        }
    },

    methods: {
        /**
         * check if removedRelations contains object with a specific id
         *
         * @param {Number} id
         *
         * @return {Boolean}
         */
        isRemoved(id) {
            return this.removedRelations.filter((obj) => {
                return id === obj.id;
            }).length ? true : false;
        },

        /**
         * check if relatedObjects contains object with a specific id
         *
         * @param {Number} id
         *
         * @return {Boolean}
         */
        isRelated(id) {
            return this.relatedObjects.filter((relatedObject) => {
                return id === relatedObject.id;
            }).length ? true : false;
        },
    }
}
