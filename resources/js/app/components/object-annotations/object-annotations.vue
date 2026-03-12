<template>
    <div class="object-annotations">
        <div
            class="loading"
            v-if="loading"
        >
            <span class="is-loading-spinner" />
            <span class="ml-05">{{ msgLoading }} ...</span>
        </div>
        <div v-if="list?.length === 0 && !loading">{{ msgNoItemsFound }}</div>
        <div v-for="item in list" :key="item.id">
            <div class="annotation-message">{{ item.attributes.description }}</div>
            <div class="annotation-meta">
                <a v-if="getAuthorUrl(item)" :href="getAuthorUrl(item)">
                    {{ getAuthor(item) }}
                </a>
                <span v-else>{{ getAuthor(item) }}</span>,
                {{ getCreated(item) }}
            </div>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ObjectAnnotations',
    props: {
        objectId: {
            type: Number,
            required: true,
        },
        userId: {
            type: Number,
            required: false,
            default: null,
        },
        userRoles: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data() {
        return {
            list: [],
            loading: false,
            msgLoading: t`Loading`,
            msgNoItemsFound: t`No items found`,
            msgUser: t`User`,
        };
    },
    mounted() {
        this.fetchAnnotations();
    },
    methods: {
        async fetchAnnotations() {
            try {
                this.loading = true;
                const baseUrl = new URL(BEDITA.base).pathname;
                const response = await fetch(`${baseUrl}api/annotations?filter[object_id]=${this.objectId}`, {
                    credentials: 'same-origin',
                    headers: {
                        accept: 'application/json',
                    }
                });
                const json = await response.json();
                this.list = json.data;
            } catch (error) {
                console.error('Error fetching annotations:', error);
            } finally {
                this.loading = false;
            }
        },
        getAuthor(item) {
            return item.attributes.params?.author || `${this.msgUser} ${item.meta?.user_id}`;
        },
        getAuthorUrl(item) {
            if (!this.userRoles.includes('admin') && !BEDITA?.canReadUsers) {
                return null;
            }
            return `/users/view/${item.meta?.user_id}`;
        },
        getCreated(item) {
            return this.$helpers.formatDate(item.meta.created);
        },
    },
};
</script>
<style scoped>
.object-annotations {
    margin-top: 1em;
}
.annotation-message {
    font-size: 1em;
    margin-bottom: 0.25em;
}
.annotation-meta {
    font-size: 0.85em;
    color: #666;
    margin-bottom: 1em;
}
</style>
