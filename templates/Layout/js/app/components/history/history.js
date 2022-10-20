import moment from 'moment';
import { t } from 'ttag';
import { PanelEvents } from '../panel-view';
import { confirm, prompt } from 'app/components/dialog/dialog';

const LOCALE = BEDITA.locale.slice(0, 2);

/**
 * Component to fetch and display changes' history.
 */
export default {
    template: `<div class="history-content" style="--module-color: ${BEDITA.currentModule.color}">
        <div v-if="isLoading" class="is-loading-spinner"></div>
        <details v-if="sortedDates.length" v-for="date in sortedDates" open>
            <summary class="pb-05 is-uppercase has-font-weight-bold"><: date :></summary>
            <ul class="history-items">
                <li class="history-item is-expanded py-05 has-border-gray-600" v-for="item in history[date]">
                    <div class="change-time"><: getFormattedTime(item.meta.created) :></div>
                    <div class="is-flex">
                        <: getAuthor(item.meta) :>
                    </div>
                    <div class="is-flex">
                        <button class="button button-text-white is-width-auto" @click.stop.prevent="showChanges(item, canSave)">info</button>
                        <button v-if="canSave" class="button button-text-white is-width-auto" @click.stop.prevent="onRestore(item.id)">${t`Restore`}</button>
                        <button v-if="canSave" class="button button-text-white is-width-auto" @click.stop.prevent="onClone(item.id)">${t`Clone`}</button>
                    </div>
                </li>
            </ul>
        </details>
        <div v-if="!isLoading && !sortedDates.length" open>
            <label>${t`No History data found`}</label>
        </div>
    </div>`,

    data() {
        return {
            history: [],
            rawHistory: [],
            isLoading: false,
            canSave: true,
        };
    },

    props: {
        object: Object,
        cansave: Boolean,
    },

    methods: {
        getFormattedTime: function (date) {
            return moment(date).format('kk:mm');
        },
        getAuthor(meta) {
            const user = meta.user;
            const application = meta.application_name;
            const name = this.getAuthorName(user);
            if (!name) {
                return '';
            }
            if (application) {
                return t`by ${name} via ${application}`;
            }

            return t`by ${name}`;
        },
        /**
         * Get formatted user name.
         * @param {Object} user User data
         * @return {string}
         */
        getAuthorName: function (user) {
            if (!user?.attributes) {
                return '';
            }

            return user?.attributes?.title || `${user?.attributes?.name || ''} ${user?.attributes?.surname || ''}`.trim() || user?.attributes?.username || '';
        },
        /**
         * Open panel to show changes.
         * @param {Object} item History item changes
         * @param {Boolean} cansave Can save toggle
         */
        showChanges(item, cansave) {
            const data = {...item, cansave};
            PanelEvents.requestPanel({
                action: 'history-info',
                from: this,
                data,
            });
            PanelEvents.listen('history-info:restore', this, this.onRestore);
        },
        /**
         * Set the URL to restore data from a point of the history and refresh the form.
         * Ask for confirmation first.
         * @param {string} historyId ID of the History item to restore
         */
        onRestore(historyId) {
            confirm(
                t`Restored data will replace current data (you can still check the data before saving). Are you sure?`,
                t`yes, proceed`,
                () => window.location.replace(`${window.location.origin}${window.location.pathname}/history/${historyId}`)
            );
        },
        /**
         * Open a new tab with the URL to create a new object with data restored from `historyId` object.
         * @param {string} historyId ID of the history item to restore
         */
        onClone(historyId) {
            let title = document.getElementById('title').value || t`Untitled`;
            let msg = t`Please insert a new title on "${title}" clone`;
            let defaultTitle = title + '-' + t`copy`;

            prompt(msg, defaultTitle, (cloneTitle, dialog) => {
                let origin = window.location.origin;
                let path = window.location.pathname.replace('/view/', '/clone/');
                let url = `${origin}${path}/history/${historyId}?title=${cloneTitle || defaultTitle}`;
                let newTab = window.open(url, '_blank');
                newTab.focus();
                dialog.hide();
            });
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
        moment.locale(LOCALE);
        const baseUrl = new URL(BEDITA.base).pathname;
        const options = {
            credentials: 'same-origin',
            headers: {
                accept: 'application/json',
            }
        };

        this.isLoading = true;
        this.canSave = this.cansave;
        const historyRes = await fetch(`${baseUrl}${this.object.type}/history/${this.object.id}`, options);
        const historyJson = await historyRes.json();
        this.rawHistory = historyJson.data;

        // only if history data found, elaborate it
        if (this.rawHistory.length) {

            // fetch users involved in the object history
            let usersId = this.rawHistory.map((change) => change.meta.user_id);
            usersId = [...new Set(usersId)]; // remove duplicates

            if (BEDITA.canReadUsers) {
                const userRes = await fetch(`${baseUrl}api/users?filter[id]=${usersId.join(',')}`, options);
                const userJson = await userRes.json();
                const users = userJson.data;

                // group changes by date
                this.history = this.rawHistory.reduce((accumulator, item) => {
                    item.meta.user = users.find((user) => user.id == item.meta.user_id);
                    const createdDate = moment(item.meta.created).format('DD MMM YYYY');
                    accumulator[createdDate] = accumulator[createdDate] || [];
                    accumulator[createdDate].push(item);
                    return accumulator;
                }, {});
            } else {
                this.history = this.rawHistory.reduce((accumulator, item) => {
                    item.meta.user = {};
                    const createdDate = moment(item.meta.created).format('DD MMM YYYY');
                    accumulator[createdDate] = accumulator[createdDate] || [];
                    accumulator[createdDate].push(item);
                    return accumulator;
                }, {});
            }

            // sort changes by time in descending order
            Object.keys(this.history).forEach((date) => this.history[date].reverse());
        }

        this.$emit('count', historyJson.meta.pagination.count);
        this.isLoading = false;
    },
}
