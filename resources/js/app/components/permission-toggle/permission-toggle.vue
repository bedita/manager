<template>
    <div v-if="hasPermissions && !userRoles.includes('admin')">
        <span class="ml-05 mr-05">{{ msgForbidden }}</span>
        <button class="button button-outlined" @click.prevent="toggleShowForbidden">
            <app-icon icon="carbon:view" v-if="showForbidden"></app-icon>
            <app-icon icon="carbon:view-off" v-else></app-icon>
            <app-icon icon="carbon:locked" class="ml-05"></app-icon>
        </button>
    </div>
</template>

<script>
import { AppEvents } from 'app/app-events';
import { t } from 'ttag';

export default {
    name: 'permission-toggle',

    props: {
        hasPermissions: {
            type: Boolean,
            default: false,
        },
        userRoles: {
            type: Array,
            default: () => ([]),
        },
    },

    data() {
        return {
            showForbidden: true,
        };
    },

    computed: {
        msgForbidden() {
            return this.showForbidden ? t`Show all folders` : t`Hide forbidden folders`;
        },
    },

    async created() {
        this.showForbidden = await this.getShowForbidden();
        AppEvents.emit('toggle-forbidden', this.showForbidden);
    },

    methods: {
        async getShowForbidden() {
            try {
                const response = await fetch(`${new URL(BEDITA.base).pathname}session/showForbidden`);

                return (await response.json())?.value || true;
            } catch (e) {
                console.error('Error retrieving session variable', e);

                return true;
            }
        },

        toggleShowForbidden() {
            this.showForbidden = !this.showForbidden;
            AppEvents.emit('toggle-forbidden', this.showForbidden);
            const options = {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                    'content-type': 'application/json',
                    'X-CSRF-Token': BEDITA.csrfToken,
                },
                body: JSON.stringify({
                    name: 'showForbidden',
                    value: this.showForbidden,
                }),
            };
            fetch(`${new URL(BEDITA.base).pathname}session`, options);
        },
    },
};
</script>
