/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relation-view> component used for ModulesPage -> View
 *
 */

Vue.component('relation-view', {
    mixins: [ PaginatedRelationMixin ],

    props: ['relationName', 'loadOnStart'],

    created() {
        if (this.loadOnStart) {
            this.getRelatedObjects();
        }
    }

});
