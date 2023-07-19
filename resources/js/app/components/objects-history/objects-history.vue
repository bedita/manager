<template>
    <div class="objectsHistory">
        <div v-if="pagination" class="filterContainer">
            <div class="filterDate">
                <label class="mr-05">
                    {{ msgStartingDate }}
                </label>
                <input type="date" v-model="filterDate" @change="changeDate" />
            </div>
            <div class="paginationContainer ml-2">
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
            <span class="column-header">
                <Icon icon="carbon:calendar"></Icon>
                <i class="ml-05">{{ msgDate }}</i>
            </span>
            <span class="column-header">
                <Icon icon="carbon:information"></Icon>
                <i class="ml-05">{{ msgInfo }}</i>
            </span>
            <span class="column-header">
                <Icon icon="carbon:content-view"></Icon>
                <i class="ml-05">{{ msgChange }}</i>
            </span>
            <template v-for="item,key in items">
                <span>{{ formatDate(item?.meta?.created) }}</span>
                <span>
                    {{ msgAction }}
                    <b class="action">{{ item?.meta?.user_action || '' }}</b>
                    {{ msgByUser }}
                    <a class="tag has-background-module-users" :href="`/users/view/${item?.meta?.user_id}`">{{ truncate(item?.meta?.user || '', 50) }}</a>
                    {{ msgWithApplication }}
                    <b class="application">{{ truncate(item?.meta?.application || '', 50) }}</b>
                    {{ msgOnResource }}
                    <a :class="`tag has-background-module-${item?.meta?.resource_type || ''}`" :href="`/view/${item.meta.resource_id}`">{{ truncate(item?.meta?.resource || '', 50) }}</a>
                </span>
                <span>
                    <json-editor
                        :options="jsonEditorOptions"
                        :target="`item-container-${item.id}`"
                        :text="JSON.stringify(item?.meta?.changed)">
                    </json-editor>
                    <div :id="`item-container-${item.id}`"></div>
                </span>
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
    props: {
        applications: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            applicationsMap: {},
            filterDate: '',
            jsonEditorOptions: {
                mainMenuBar: false,
                navigationBar: false,
                statusBar: false,
            },
            items: [],
            loading: false,
            msgAction: t`Action`,
            msgByUser: t`by user`,
            msgChange: t`Change`,
            msgDate: t`Date`,
            msgHistory: t`History items`,
            msgInfo: t`Info`,
            msgOnResource: t`on resource`,
            msgResource: t`Resource`,
            msgStartingDate: t`Starting date`,
            msgWithApplication: t`with application`,
            pagination: {},
            resourcesMap: {},
            resourcesTypesMap: {},
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.filterDate = this.$helpers.getLastWeeksDate().toISOString().split('T')[0];
            this.loadHistory();
            this.applicationsMap = this.applications.reduce((map, obj) => {
                map[obj?.id] = obj.attributes.name;
                return map;
            }, {});
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
        async getObjects(ids) {
            return fetch(`/api/objects?filter[id]=${ids.join(',')}`).then((r) => r.json());
        },
        loadHistory(pageSize = 20, page = 1) {
            this.loading = true;
            this.getHistory(pageSize, page)
                .catch(_error => { this.loading = false; return false;})
                .then(response => {
                    this.loading = false;
                    this.items = response.data || [];
                    this.pagination = response.meta?.pagination || {};
                    const userIds = this.items.map(item => item.meta.user_id).filter((v, i, a) => a.indexOf(v) === i).map(i=>Number(i));
                    const objectIds = this.items.map(item => item.meta.resource_id).filter((v, i, a) => a.indexOf(v) === i).map(i=>Number(i));
                    const ids = [...userIds, ...objectIds];
                    this.getObjects(ids)
                        .catch(_error => { return false;})
                        .then(response => {
                            const items = response.data || [];
                            const resourcesMap = items.reduce((map, obj) => {
                                if (obj.type === 'users') {
                                    map[obj?.id] = obj?.attributes?.title || obj?.attributes?.username || obj?.id;
                                } else {
                                    map[obj?.id] = obj?.attributes?.title || obj?.id;
                                }
                                return map;
                            }, {});
                            this.resourcesMap = {...this.resourcesMap, ...resourcesMap};
                            const resourcesTypesMap = items.reduce((map, obj) => {
                                map[obj?.id] = obj?.type;
                                return map;
                            }, {});
                            this.resourcesTypesMap = {...this.resourcesTypesMap, ...resourcesTypesMap};
                            for (const item of this.items) {
                                item.meta.user = this.resourcesMap[item.meta.user_id] || item.meta.user_id;
                                item.meta.resource = this.resourcesMap[item.meta.resource_id] || item.meta.resource_id;
                                item.meta.application = this.applicationsMap[item.meta.application_id] || item.meta.application_id;
                                item.meta.resource_type = this.resourcesTypesMap[item.meta.resource_id] || item.meta.resource_type;
                            }
                            this.$forceUpdate();
                        });
                }
            );
        },
        msgActionBy(item) {
            const action = item?.meta?.user_action || '';

            return t`Action ${action} performed by`;
        },
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
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
    grid-template-columns: 200px repeat(2, 1fr);
    border-top: 1px dotted black;
    border-right: 1px dotted black;
}
.objectsHistory > .grid > span {
    padding: 4px 8px;
    border-left: 1px dotted black;
    border-bottom: 1px dotted black;
    white-space: normal;
}
.objectsHistory > .grid > span.column-header {
    text-align: left;
}
.objectsHistory > .grid > span > a:hover {
    text-decoration: underline;
}
span.column-header {
    color: yellow;
    text-align: center;
}
b.application, b.action {
    color: white;
}
</style>
