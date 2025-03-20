<template>
    <div class="history-info">
        <section class="fieldset shrinks">
            <header class="mx-1 tab tab-static unselectable">
                <h2>
                    <span v-if="authorName">{{ msgVersionBy }}</span>
                    <span
                        class="has-font-weight-bold"
                        v-if="authorName"
                    >
                        {{ authorName }}
                    </span>
                    <span>{{ msgDate }} {{ formattedDate }}</span>
                </h2>
            </header>
            <div class="object-form px-1 shrinks">
                <h4 v-if="user_action == 'create'">
                    {{ msgCreated }}:
                </h4>
                <h4 v-if="user_action == 'update'">
                    {{ msgUpdated }}:
                </h4>
                <h4 v-if="user_action == 'trash'">
                    {{ msgTrashed }}
                </h4>
                <h4 v-if="user_action == 'restore'">
                    {{ msgRestored }}
                </h4>
                <div class="fieldset">
                    <div
                        v-for="(value, key) in changedAllowed"
                        :key="key"
                        class="container"
                        v-html="value"
                    />
                    <button
                        class="mt-2"
                        @click.stop.prevent="restore()"
                        v-if="canSave"
                    >
                        {{ msgRestore }}
                    </button>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';
import { PanelEvents } from '../panel-view';
export default {
    name: 'HistoryInfo',
    props: {
        meta: {
            type: Object,
            default: () => ({}),
        },
        id: {
            type: String,
            default: null,
        },
        cansave: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            changed: this.meta.changed,
            changedAllowed: {},
            created: this.meta.created,
            user: this.meta.user,
            user_action: this.meta.user_action,
            canSave: true,
            msgCreated: t`Created`,
            msgDate: t`date`,
            msgUpdated: t`Updated`,
            msgTrashed: t`Trashed`,
            msgRestored: t`Restored`,
            msgRestore: t`Restore`,
            msgVersionBy: t`version by`,
        }
    },
    computed: {
        authorName: function() {
            if (!this.user?.attributes) {
                return;
            }
            const user = this.user.attributes;
            return user.title ||
                `${user.name || ''} ${user.surname || ''}`.trim() ||
                user.username ||
                '';
        },
        formattedDate: function() {
            return moment(this.created).format('D MMM YYYY kk:mm');
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.changedAllowed = {...this.changed};
            const keys = Object.keys(this.changed);
            for (const key in keys) {
                if (['_csrfToken', 'model-type'].includes(key)) {
                    delete this.changedAllowed[key];
                }
            }
        });
    },
    async created() {
        this.canSave = this.cansave;
    },
    methods: {
        restore() {
            PanelEvents.sendBack('history-info:restore', this.id);
        },
    }
}
</script>
