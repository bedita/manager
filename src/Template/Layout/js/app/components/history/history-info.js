import moment from 'moment';
import { t } from 'ttag';
import { PanelEvents } from '../panel-view';

export default {
    template: `<div class="history-info">
        <section class="fieldset shrinks">
            <header class="mx-1 tab tab-static unselectable">
                <h2>
                    <span>${t`version by`}</span>
                    <span class="has-font-weight-bold"><: authorName :></span>
                    <span>${t`date`} <: formattedDate :></span>
                </h2>
            </header>
            <div class="object-form px-1 shrinks">
                <h4 v-if="user_action == 'create'">${t`Created`}:</h4>
                <h4 v-if="user_action == 'update'">${t`Updated`}:</h4>
                <h4 v-if="user_action == 'trash'">${t`Trashed`}</h4>
                <h4 v-if="user_action == 'restore'">${t`Restored`}</h4>
                <div class="fieldset">
                    <div v-for="(value, key) in changed" v-html="value" class="container" v-if="allowed(key)"></div>

                    <button v-if="canSave" class="mt-2" @click.stop.prevent="restore()">${t`Restore`}</button>
                </div>
            </div>
        </section>
    </div>`,

    props: {
        meta: Object,
        id: String,
        cansave: Boolean,
    },

    data() {
        return {
            changed: this.meta.changed,
            created: this.meta.created,
            user: this.meta.user,
            user_action: this.meta.user_action,
            canSave: true,
        }
    },

    computed: {
        formattedDate: function() {
            return moment(this.created).format('D MMM YYYY kk:mm');
        },
        /**
         * Get formatted user name.
         * @param {Object} userObj User data
         * @return {string}
         */
        authorName: function() {
            if (!this.user) {
                return;
            }
            const user = this.user.attributes;

            return user.title ||
                `${user.name || ''} ${user.surname || ''}`.trim() ||
                user.username ||
                '';
        },
    },

    async created() {
        this.canSave = this.cansave;
    },

    methods: {
        allowed(key) {
            return (key !== 'model-type' && key !== '_csrfToken');
        },
        restore() {
            PanelEvents.sendBack('history-info:restore', this.id);
        },
    }
}
