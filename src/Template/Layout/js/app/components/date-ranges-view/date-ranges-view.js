import moment from 'moment';

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/calendar.twig
 *
 * <date-ranges-view> component used for ModulesPage -> View
 */
export default {
    props: ['ranges'],
    /**
     * @inheritDoc
     */
    data() {
        return {
            dateRanges: [],
        }
    },
    created() {
        const ranges = JSON.parse(this.ranges);
        if (ranges) {
            this.dateRanges = ranges;
            this.dateRanges.forEach((range, index) => {
                range.params = range.params || { all_day: false };
            });
        }
        if (!this.dateRanges.length) {
            this.add();
        }
    },
    /**
     * @inheritDoc
     */
    methods: {
        /**
         * Add an empty date range to list.
         *
         * @returns {void}
         */
        add() {
            this.dateRanges.push({
                start_date: '',
                end_date: '',
                params: {
                    all_day: false,
                },
            });
        },
        /**
         * Handle date change.
         *
         * @param {Object} dateRange The date range object
         * @param {string} value The date value.
         * @returns {void}
         */
        onDateChanged(dateRange, { target: { value } }) {
            if (!value || !dateRange.params.all_day) {
                return;
            }
            this.setAllDayRange(dateRange);
        },
        /**
         * Handle changes to `all_day` value of a date range.
         * When checked, the range must start at 00:00 and end at 23:59 of the `start_date`
         * (or `end_date` if it's the only date).
         *
         * @param {Object} dateRange Date range object.
         * @param {boolean} checked The value of `all_day` flag
         * @returns {void}
         */
        onAllDayChanged(dateRange, { target: { checked } }) {
            if (!checked) {
                return;
            }
            this.setAllDayRange(dateRange);
        },
        /**
         * Adjust range dates to be a single full day event.
         * The range must start at 00:00 and end at 23:59 of the `start_date`
         * (or `end_date` if it's the only date).
         *
         * @param {Object} dateRange Date range object.
         * @returns {void}
         */
        setAllDayRange(dateRange) {
            if (!dateRange.start_date && !dateRange.end_date) {
                return;
            }
            let date = moment(dateRange.start_date || dateRange.end_date);
            date.set({ hour: 0, minute: 0, second: 0, millisecond: 0 });
            dateRange.start_date = date.format();
            dateRange.end_date = date.endOf('day').format();
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
