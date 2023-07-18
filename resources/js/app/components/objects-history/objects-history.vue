<template>
    <div class="objectsHistory">
        <div v-if="pagination" class="filterContainer">
            <div class="filterDate">
                <label>
                    {{ msgStartingDate }}
                    <input type="date" v-model="filterDate" @change="changeDate" />
                </label>
            </div>
            <div class="paginationContainer">
                <PaginationNavigation
                    :pagination="pagination"
                    :resource="msgHistory"
                    @change-page-size="changePageSize"
                    @change-page="changePage">
                </PaginationNavigation>
            </div>
        </div>
        <span v-if="loading" class="is-loading-spinner"></span>
        <div v-if="!loading && items.length > 0"  class="grid">
            <span>#</span>
            <span class="column-header">{{ msgAction }}</span>
            <span class="column-header">{{ msgChanged }}</span>
            <span class="column-header">{{ msgWhen }}</span>
            <span class="column-header">{{ msgResource }}</span>
            <span class="column-header">{{ msgUser }}</span>
            <span class="column-header">{{ msgApplication }}</span>
            <template v-for="item,key in items">
                <span>{{ (pagination.page_size * (pagination.page - 1)) + key + 1 }}</span>
                <span>{{ item?.meta?.user_action }}</span>
                <span>
                    <json-editor
                        :options="jsonEditorOptions"
                        :target="`item-container-${item.id}`"
                        :text="JSON.stringify(item?.meta?.changed)">
                    </json-editor>
                    <div :id="`item-container-${item.id}`"></div>
                </span>
                <span>{{ formatDate(item?.meta?.created) }}</span>
                <span><a :href="`/view/${item.meta.resource_id}`">{{ item?.meta?.resource_id }}</a></span>
                <span><a :href="`/users/view/${item?.meta?.user_id}`">{{ item?.meta?.user_id }}</a></span>
                <span>{{ item?.meta?.application_id }}</span>
            </template>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ObjectsHistory',
    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
        PaginationNavigation:() => import(/* webpackChunkName: "pagination-navigation" */'app/components/pagination-navigation/pagination-navigation'),
    },
    data() {
        return {
            filterDate: '2023-01-01',
            jsonEditorOptions: {
                mainMenuBar: false,
                navigationBar: false,
                statusBar: false,
            },
            items: [],
            loading: false,
            msgAction: t`Action`,
            msgApplication: t`Application`,
            msgChanged: t`Changed`,
            msgHistory: t`History items`,
            msgResource: t`Resource`,
            msgStartingDate: t`Starting date`,
            msgUser: t`User`,
            msgWhen: t`When`,
            pagination: {},
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.loadHistory();
        });
    },
    methods: {
        changeDate() {
            this.loadHistory(this.pagination.page_size, this.pagination.page);
        },
        changePage(page) {
            this.loadHistory(this.pagination.page_size, page);
        },
        changePageSize(pageSize) {
            this.loadHistory(pageSize);
        },
        formatDate(d) {
            return d ?  new Date(d).toLocaleDateString() + ' ' + new Date(d).toLocaleTimeString() : '';
        },
        async getHistory(pageSize = 20, page = 1) {
            const filterDate = this.filterDate ? new Date(this.filterDate).toISOString() : '';

            return fetch(`/api/history?page_size=${pageSize}&page=${page}&filter[created][gt]=${filterDate}`).then((r) => r.json());
        },
        loadHistory(pageSize = 20, page = 1) {
            this.loading = true;
            this.getHistory(pageSize, page)
                .catch(_error => { this.loading = false; return false;})
                .then(response => {
                    this.loading = false;
                    this.items = response.data || [];
                    this.pagination = response.meta?.pagination || {};
                }
            );
        },
    }
}
</script>
<style>
.objectsHistory {
    min-width: 500px;
    max-width: 1200px;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
}
.objectsHistory > .filterContainer {
    display: inline-flex;
    padding-bottom: 1rem;
}
.objectsHistory > .grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-top: 1px dotted black;
    border-right: 1px dotted black;
}
.objectsHistory > .grid > span {
    padding: 4px 8px;
    border-left: 1px dotted black;
    border-bottom: 1px dotted black;
}
.objectsHistory > .grid > span > a:hover {
    text-decoration: underline;
}
span.column-header {
    color: yellow;
    text-align: center;
}
</style>
