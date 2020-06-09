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
            emptyDateRange: {
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
        if (!this.dateRanges.length) {
            this.add();
        }
    },

    /**
     * @inheritDoc
     */
    methods: {

        /**
         * Add date range to list.
         *
         * @param {Event} e The event
         * @returns {void}
         */
        add() {
            this.dateRanges.push(Vue.util.extend({}, this.emptyDateRange));
        },

        /**
         * Remove date range from list.
         *
         * @param {Integer} index The index
         * @param {Event} e The event
         * @returns {void}
         */
        remove(index) {
            this.dateRanges.splice(index, 1);
        },
    },
}
