import flatpickr from 'flatpickr';

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/Form/date-ranges.twig
 *
 * <date-ranges-list> component used for ModulesPage -> Index
 */
export default {

    /**
     * @inheritDoc
     */
    data() {
        return {
            dateRanges: [],
            toggle: false,
        };
    },

    /**
     * @inheritDoc
     */
    mounted() {
        this.dateRanges = JSON.parse(this.$el.dataset.items);
        this.toggle = false;
    },

    /**
     * @inheritDoc
     */
    methods: {
        format(date) {
            return flatpickr.formatDate(new Date(date), 'Y-m-d h:i K');
        },
    },
}
