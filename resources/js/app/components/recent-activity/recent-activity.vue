<template>
    <section class="dashboard-section">
        <header>
            <h2>{{ msgRecentActivity }}</h2>
        </header>

        <div v-if="pagination">
            <nav class="pagination has-text-size-smallest">
                <div class="count-items">
                    <span class="has-font-weight-bold">{{ pagination.count }}</span>
                    <span>{{ msgItems }}</span>
                </div>
                <div class="page-size">
                    <span>{{ msgSize }}</span>
                    <select
                        class="page-size-selector has-background-gray-700 has-border-gray-700 has-font-weight-light has-text-gray-200 has-text-size-smallest"
                        v-model="pageSize"
                        @change="changePage"
                    >
                        <option
                            v-for="pSize in pageSizes"
                            :key="pSize"
                        >
                            {{ pSize }}
                        </option>
                    </select>
                </div>
                <div class="pagination-buttons">
                    <div>
                        <!-- first page -->
                        <button :class="pageButtonClass" @click.prevent="changePage(1)" v-if="pagination.page > 1">{{ 1 }}</button>
                        <!-- delimiter -->
                        <span class="pages-delimiter" v-if="pagination.page > 3" />
                        <!-- prev page -->
                        <button :class="pageButtonClass" @click.prevent="changePage(pagination.page - 1)" v-if="pagination.page > 2">{{ pagination.page - 1 }}</button>
                        <!-- current page -->
                        <input size="2" class="ml-05" :class="pagination.page === 1 ? 'mr-05' : 'ml-05'" v-model="currentPage" />
                        <!-- next page -->
                        <button :class="pageButtonClass" @click.prevent="changePage(pagination.page + 1)" v-if="pagination.page < pagination.page_count - 1">{{ pagination.page + 1 }}</button>
                        <!-- delimiter -->
                        <span class="pages-delimiter" v-if="pagination.page < pagination.page_count - 2" />
                        <!-- last page -->
                        <button :class="pageButtonClass" @click.prevent="changePage(pagination.page_count)" v-if="pagination.page < pagination.page_count">{{ pagination.page_count }}</button>
                    </div>
                </div>
            </nav>
        </div>
        <div class="is-loading-spinner mt-05" v-if="loading" />
        <div class="list-objects" v-if="items.length > 0 && !loading">
            <div class="table-row">
                <div class="narrow">{{ msgTitle }}</div>
                <div class="narrow">{{ msgType }}</div>
                <div class="narrow">{{ msgAction }}</div>
                <div class="narrow">{{ msgChanged }}</div>
                <div class="narrow">{{ msgDate }}</div>
            </div>
            <template v-for="item in items">
                <a :key="item.id" v-if="item.object_type" :href="url(item)" class="table-row" :class="`object-status-${item.object_status}`">
                    <div class="narrow">{{ title(item) }}</div>
                    <div class="type-cell"><span :class="`tag has-background-module-${item.object_type}`">{{ t(item.object_type || '?') }}</span></div>
                    <div class="narrow">{{ item.meta.user_action }}</div>
                    <div class="narrow" :title="changes(item, false)">{{ changes(item) }}</div>
                    <div class="narrow">{{ formatDate(item.meta.created) }}</div>
                </a>
                <div class="table-row object-status-deleted" v-else>
                    <div class="narrow">{{ title(item) }}</div>
                    <div class="type-cell"><span :class="`tag`">?</span></div>
                    <div class="narrow">{{ item.meta.user_action }}</div>
                    <div class="narrow" :title="changes(item, false)">{{ changes(item) }}</div>
                    <div class="narrow">{{ formatDate(item.meta.created) }}</div>
                </div>
            </template>
        </div>
        <div v-if="!items.length">
            {{ msgNoItemsFound }}
        </div>
    </section>
</template>
<script>
import { t } from 'ttag';

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

export default {
    name: 'RecentActivity',

    props: {
        pageSizes: {
            type: Array,
            default: () => [10, 20, 50, 100, 500],
        },
        userId: {
            type: Number,
            default: 0,
        },
    },

    data() {
        return {
            currentPage: 1,
            items: [],
            loading: false,
            pageSize: null,
            pagination: {},
            msgAction: t`Action`,
            msgActivity: t`Activity`,
            msgChanged: t`Changed`,
            msgDate: t`Date`,
            msgItems: t`items`,
            msgNoItemsFound: t`No items found`,
            msgRecentActivity: t`Your recent activity`,
            msgSize: t`Size`,
            msgTitle: t`Title or #id uname`,
            msgType: t`Type`,
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.pageSize = this.pageSizes[0];
            this.changePage();
        });
    },

    methods: {
        async changePage(page = 1) {
            await this.fetchObjects(this.userId, page, this.pageSize);
        },
        changes(item, truncate = true) {
            if (truncate) {
                return this.$helpers.truncate(Object.keys(item.meta.changed).join(', '), 50);
            }

            return Object.keys(item.meta.changed).join(', ');
        },
        async fetchObjects(userId, page, pageSize) {
            this.loading = true;
            const response = await fetch(`${API_URL}api/history?filter[user_id]=${userId}&page=${page}&page_size=${pageSize}&sort=-created`, API_OPTIONS);
            const json = await response.json();
            this.pagination = json.meta.pagination;
            this.currentPage = json.meta.pagination.page;
            this.items = [...(json.data || [])];
            const ids = this.items.filter(item => item.meta.resource_id).map(item => item.meta.resource_id).filter((v, i, a) => a.indexOf(v) === i).map(i=>Number(i));
            console.log(ids);
            const objectResponse = await fetch(`${API_URL}api/objects?filter[id]=${ids.join(',')}&page_size=${pageSize}`, API_OPTIONS);
            const objectJson = await objectResponse.json();
            for (const item of this.items) {
                const object = objectJson.data.find(obj => obj.id === item.meta.resource_id);
                if (!object) {
                    continue;
                }
                item.object_title = object.attributes.title;
                item.object_uname = object.attributes.uname;
                item.object_status = object.attributes.status;
                item.object_type = object.type;
            }
            this.loading = false;
        },
        formatDate(d) {
            return d ?  new Date(d).toLocaleDateString() + ' ' + new Date(d).toLocaleTimeString() : '';
        },
        pageButtonClass() {
            return 'has-text-size-smallest button is-width-auto button-outlined';
        },
        title(item) {
            if (item.object_title) {
                return this.$helpers.truncate(item.object_title, 100);
            }
            const id = `#${item.meta.resource_id}`;
            const uname = item.object_uname ? this.$helpers.truncate(item.object_uname, 100) : t`(deleted)`;

            return `${id} ${uname}`;
        },
        url(item) {
            return `${item.object_type}/view/${item.meta.resource_id}`;
        },
    },
}
</script>
