/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/Modules/index_properties_date_ranges.twig
 *
 * <date-ranges-list> component used for ModulesPage -> Index
 */
export default {

    components: {
        // icons
        IconAdd: () => import(/* webpackChunkName: "icon-add" */'@carbon/icons-vue/es/add/16.js'),
        IconSubtract: () => import(/* webpackChunkName: "icon-subtract" */'@carbon/icons-vue/es/subtract/16.js'),
    },

    data() {
        return {
            showAll: false,
        };
    },
}
