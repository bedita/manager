import Vue from 'vue';

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/calendar.twig
 *
 * <date-ranges-view> component used for ModulesPage -> View
 */
export default {
    /**
     * @inheritDoc
     */
    data() {
        return {
            dateRange: {
                start_date: '',
                end_date: '',
                params: '',
            },
            dateRanges: [],
        };
    },
    /**
     * @inheritDoc
     */
    mounted() {
        this.dateRanges = JSON.parse(this.$el.dataset.items);
    },
    /**
     * @inheritDoc
     */
    methods: {
        /**
         * Add date range to list.
         *
         * @returns {void}
         */
        add(e) {
            e.preventDefault();
            this.dateRanges.push(Vue.util.extend({}, this.dateRange));
        },
    },
}
