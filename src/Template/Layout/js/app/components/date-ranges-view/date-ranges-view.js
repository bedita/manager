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
                params: {
                    all_day: false
                },
            },
            dateRanges: [],
        };
    },

    /**
     * @inheritDoc
     */
    mounted() {
        this.dateRanges = JSON.parse(this.$el.dataset.items);
        this.dateRanges.forEach(element => {
            if (element.params === null) {
                element.params = { all_day: false };
            }
        });
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
         * @returns {void}
         */
        add() {
            this.dateRanges.push(Vue.util.extend({}, this.emptyDateRange));
        },

        /**
         * Remove date range from list.
         *
         * @param {Integer} index The index
         * @returns {void}
         */
        remove(index) {
            this.dateRanges.splice(index, 1);
        },
    },
}
