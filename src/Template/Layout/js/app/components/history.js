import moment from 'moment';
import { t } from 'ttag';

const LOCALE = BEDITA.locale.slice(0, 2);

/**
 * Component to fetch and display changes' history.
 */
export default {
    template: `<div class="history-content" style="--module-color: ${BEDITA.currentModule.color}">
        <div v-if="isLoading" class="is-loading-spinner"></div>
        <details v-for="date in sortedDates" open>
            <summary class="pb-05 has-text-transform-upper has-font-weight-bold"><: date :></summary>
            <ul class="history-items">
                <li class="history-item is-expanded py-05 has-border-gray-600" v-for="item in history[date]">
                    <div class="is-flex"><: getAuthorName(item.user) :></div>
                    <div class="is-flex">
                        <span class="mr-1 is-capitalized"><: getActionLabel(item.user_action) :></span>
                        <div><: Object.keys(item.changed).join(', ') :></div>
                    </div>
                    <div class="change-time"><: getFormattedTime(item.created) :></div>
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
        /**
         * Get formatted user name.
         * @param {Object} userObj User data
         * @return {string}
         */
        getAuthorName: function (userObj) {
            if (!userObj) {
                return;
            }
            const user = userObj.attributes;

            return user.title ||
                `${user.name || ''} ${user.surname || ''}`.trim() ||
                user.username ||
                '';
        },
        /**
         * Translate user action.
         * Capitalize it before translation and then add colon.
         * @param {string} action User action name
         * @return {string}
         */
        getActionLabel(action) {
            let label = t`${action.replace(/^\w/, (c) => c.toUpperCase())}d`;
            if (action !== 'trash' && action != 'restore') {
                label += ':';
            }
            return label;
        }
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
        moment.locale(LOCALE);
        const baseUrl = new URL(BEDITA.base).pathname;
        const options = {
            credentials: 'same-origin',
            headers: {
                accept: 'application/json',
            }
        };

        this.isLoading = true;
        const historyRes = await fetch(`${baseUrl}api/history?filter[resource_id]=${this.objectId}&page_size=100`, options);
        const historyJson = await historyRes.json();
        const history = historyJson.data;

        // fetch users involved in the object history
        const usersId = history.map((change) => change.meta.user_id);
        const userRes = await fetch(`${baseUrl}api/users?filter[id]=${usersId.join(',')}`, options);
        const userJson = await userRes.json();
        const users = userJson.data;

        // group changes by date
        this.history = history.reduce((accumulator, { meta: change }) => {
            change.user = users.find((user) => user.id == change.user_id);
            const createdDate = moment(change.created).format('DD MMM YYYY');
            accumulator[createdDate] = accumulator[createdDate] || [];
            accumulator[createdDate].push(change);
            return accumulator;
        }, {});

        // sort changes by time in descending order
        Object.keys(this.history).forEach((date) => this.history[date].reverse());

        this.$emit('count', historyJson.meta.pagination.count);
        this.isLoading = false;
    },
}
