/**
 * Filter Box View component
 *
 * allows to filter a list of objects with a text field, properties and a pagination toolbar
 *
 * <filter-box-view> component
 *
 * @prop {String} objectsLabel
 * @prop {String} placeholder
 * @prop {Boolean} showFilterButtons
 * @prop {Object} initFilter
 * @prop {Object} relationTypes relation types available for relation (left/right)
 * @prop {Array} filterList custom filters to show
 * @prop {Object} pagination
 * @prop {String} configPaginateSizes
 */

export default {

    props: {
        jobs: {
            type: Array,
            default: () => [],
        },
    },

    methods: {},
};
