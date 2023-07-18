<template>
    <div class="userAccesses">
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
                    :resource="msgAccesses"
                    @change-page-size="changePageSize"
                    @change-page="changePage">
                </PaginationNavigation>
            </div>
        </div>
        <span v-if="loading" class="is-loading-spinner"></span>
        <div v-if="!loading && accesses.length > 0" class="grid">
            <span>#</span>
            <span class="column-header">{{ msgLastLogin }}</span>
            <span class="column-header">{{ msgUsername }}</span>
            <template v-for="user,key in accesses">
                <span>{{ (pagination.page_size * (pagination.page - 1)) + key + 1 }}</span>
                <span>{{ formatDate(user?.meta?.last_login) }}</span>
                <span>
                    <a :href="`/users/view/${user.id}`" target="_new">
                        {{ user?.attributes?.title || user?.attributes?.username }}
                    </a>
                </span>
            </template>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'UserAccesses',
    components: {
        PaginationNavigation:() => import(/* webpackChunkName: "pagination-navigation" */'app/components/pagination-navigation/pagination-navigation'),
    },
    data() {
        return {
            accesses: [],
            filterDate: '2023-01-01',
            loading: false,
            msgAccesses: t`Accesses`,
            msgLastLogin: t`Last login`,
            msgNext: t`Next`,
            msgPage: t`Page`,
            msgPrev: t`Prev`,
            msgStartingDate: t`Starting date`,
            msgUsername: t`Username`,
            pagination: {},
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.loadAccesses();
        });
    },
    methods: {
        changeDate() {
            this.loadAccesses(this.pagination.page_size, this.pagination.page);
        },
        changePage(page) {
            this.loadAccesses(this.pagination.page_size, page);
        },
        changePageSize(pageSize) {
            this.loadAccesses(pageSize);
        },
        formatDate(d) {
            return d ?  new Date(d).toLocaleDateString() + ' ' + new Date(d).toLocaleTimeString() : '';
        },
        async getAccesses(pageSize = 20, page = 1) {
            const filterDate = this.filterDate ? new Date(this.filterDate).toISOString() : '';

            return fetch(`/api/users?page_size=${pageSize}&page=${page}&filter[last_login][gt]=${filterDate}&sort=-last_login`).then((r) => r.json());
        },
        loadAccesses(pageSize = 20, page = 1) {
            this.loading = true;
            this.getAccesses(pageSize, page)
                .catch(_error => { this.loading = false; return false;})
                .then(response => {
                    this.loading = false;
                    this.accesses = response.data || [];
                    this.pagination = response.meta?.pagination || {};
                }
            );
        },
    }
}
</script>
<style>
.userAccesses {
    min-width: 500px;
    max-width: 800px;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
}
.userAccesses > .filterContainer {
    display: inline-flex;
    padding-bottom: 1rem;
}
.userAccesses > .grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-top: 1px dotted black;
    border-right: 1px dotted black;
}
.userAccesses > .grid > span {
    padding: 4px 8px;
    border-left: 1px dotted black;
    border-bottom: 1px dotted black;
}
.userAccesses > .grid > span > a:hover {
    text-decoration: underline;
}
span.column-header {
    color: yellow;
    text-align: center;
}
</style>
