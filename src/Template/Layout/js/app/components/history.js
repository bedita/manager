import moment from 'moment';

const LOCALE = BEDITA.locale.slice(0, 2);

/**
 * Component to fetch and display changes' history.
 */
export default {
    template: `<div class="history-content" style="--module-color: ${BEDITA.currentModule.color}">
        <div v-if="isLoading" class="is-loading-spinner"></div>
        <details v-for="date in sortedDates" open>
            <summary><: date :></summary>
            <ul class="history-items">
                <li class="history-item" v-for="item in history[date]">
                    <div><: item.user_id :></div>
                    <div class="changed-properties">
                        <span class="action"><: item.user_action :></span>
                        <ul>
                            <li v-for="(value, property) in item.changed"><: property :></li>
                        </ul>
                    </div>
                    <div><: getFormattedTime(item.created) :></div>
                </li>
            </ul>
        </details>
    </div>`,

    data() {
        return {
            history: [],
            isLoading: false,
        };
    },

    props: {
        objectId: [String, Number],
    },

    methods: {
        getFormattedTime: function (date) {
            return moment(date).format('kk:mm');
        },
    },

    computed: {
        /**
         * Return sorted desc dates.
         * @return {Array}
         */
        sortedDates: function () {
            return Object.keys(this.history)
                .sort((date1, date2) => moment(date1, 'DD MMM YYYY').diff(moment(date2, 'DD MMM YYYY')))
                    .reverse();
        },
    },

    async created() {
        const baseUrl = new URL(BEDITA.base).pathname;
        const options = {
            credentials: 'same-origin',
            headers: {
                accept: 'application/json',
            }
        };
        moment.locale(LOCALE);

        this.isLoading = true;
        const response = await fetch(`${baseUrl}api/history?filter[resource_id]=${this.objectId}`, options);
        const json = await response.json();

        // group changes by date
        this.history = json.data.reduce((accumulator, change) => {
            const createdDate = moment(change.meta.created).format('DD MMM YYYY');
            accumulator[createdDate] = accumulator[createdDate] || [];
            accumulator[createdDate].push(change.meta);
            return accumulator;
        }, {});

        this.$emit('count', json.meta.pagination.count);
        this.isLoading = false;
    },
}
