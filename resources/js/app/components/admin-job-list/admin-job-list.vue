<template>
    <div class="admin-job-list">
        <h4 v-if="service !== 'all'">{{ service }}</h4>
        <div>
            <div class="toolbar ml-2 mb-2">
                <div
                    class="service-filter"
                    v-if="service === 'all'"
                >
                    <select
                        v-model="selectedService"
                        @change="onChangeServiceFilter"
                    >
                        <option value="all">
                            {{ msgAll }}
                        </option>
                        <option
                            v-for="serviceOption in serviceOptions"
                            :key="serviceOption"
                            :value="serviceOption"
                        >
                            {{ serviceOption }}
                        </option>
                    </select>
                </div>
                <div class="paginationContainer">
                    <PaginationNavigation
                        :pagination="pagination"
                        :resource="msgAsyncJobs"
                        @change-page-size="changePageSize"
                        @change-page="changePage"
                    />
                </div>
            </div>
            <div
                class="is-loading-spinner mt-05"
                v-if="loading"
            />
            <div
                class="tab-container"
                v-if="!loading && jobs?.length > 0"
            >
                <div
                    id="list-jobs"
                    :class="{ 'file-column': columnFile }"
                >
                    <div class="table-header">
                        {{ msgJob }}
                    </div>
                    <div class="table-header" v-if="columnFile">
                        {{ msgFileName }}
                    </div>
                    <div class="table-header">
                        {{ msgServiceName }}
                    </div>
                    <div class="table-header">
                        {{ msgScheduledFrom }}
                    </div>
                    <div class="table-header">
                        {{ msgCompletedOn }}
                    </div>
                    <div class="table-header">
                        {{ msgStatus }}
                    </div>
                    <div class="table-header" />
                    <template v-for="job in jobs">
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                        >
                            {{ fmt(job.meta.created) }}
                            <br>
                            {{ job.id }}
                        </div>
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                            v-if="columnFile"
                        >
                            {{ job.attributes.payload && job.attributes.payload.filename }}
                        </div>
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                        >
                            {{ job.attributes.service }}
                        </div>
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                        >
                            {{ fmt(job.attributes.scheduled_from) }}
                        </div>
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                        >
                            {{ fmt(job.meta.completed) }}
                        </div>
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                        >
                            {{ job.meta.status }}
                        </div>
                        <div
                            :class="[job.meta.status, { 'row-hover': hoveredJobId === job.id }]"
                            @mouseover="hoveredJobId = job.id"
                            @mouseleave="hoveredJobId = null"
                        >
                            <a
                                :class="showPayloadId != job.id ? 'icon-plus' : 'icon-minus'"
                                @click.prevent="togglePayload(job.id)"
                            />
                        </div>
                        <div
                            class="job-payload"
                            :class="{ 'job-payload-file': columnFile }"
                            v-show="showPayloadId == job.id"
                        >
                            <h3>Payload</h3>
                            <div :id="`container-payload-${job.id}`" />
                            <json-editor
                                :options="jsonEditorOptions"
                                :target="`container-payload-${job.id}`"
                                :text="JSON.stringify(job.attributes.payload)"
                            />

                            <h3 v-if="job.attributes.results">
                                Results
                            </h3>
                            <div
                                :id="`container-results-${job.id}`"
                                v-if="job.attributes.results"
                            />
                            <json-editor
                                :options="jsonEditorOptions"
                                :target="`container-results-${job.id}`"
                                :text="JSON.stringify(job.attributes.results)"
                                v-if="job.attributes.results"
                            />
                        </div>
                    </template>
                </div>
            </div>
            <div
                class="mt-05"
                v-if="!loading && jobs.length === 0"
            >
                {{ msgNoJobs }}
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';

export default {
    name: 'AdminJobList',

    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
        PaginationNavigation:() => import(/* webpackChunkName: "pagination-navigation" */'app/components/pagination-navigation/pagination-navigation'),
    },

    props: {
        service: {
            type: String,
            default: () => 'all',
        },
    },

    data() {
        return {
            columnFile: false,
            isOpen: false,
            jobs: [],
            jsonEditorOptions: {
                mainMenuBar: false,
            },
            loading: false,
            pagination: {
                page: 1,
                page_size: 10,
                page_count: 1,
                total_count: 0,
            },
            hoveredJobId: null,
            selectedService: 'all',
            serviceOptions: ['credentials_change', 'mail', 'signup', 'thumbnail'],
            showPayloadId: null,
            msgAll: t`All`,
            msgAsyncJobs: t`Async Jobs`,
            msgCompletedOn: t`Completed on`,
            msgFileName: t`File name`,
            msgJob: t`Job`,
            msgJobs: t`Jobs`,
            msgNoJobs: t`No Jobs`,
            msgScheduledFrom: t`Scheduled from`,
            msgServiceName: t`Service name`,
            msgStatus: t`Status`,
        };
    },

    async mounted() {
        this.columnFile = !['all', 'credentials_change', 'mail', 'signup', 'thumbnail'].includes(this.service);
        this.selectedService = this.service;
        this.$nextTick(async () => {
            this.toggleOpen();
        });
    },

    methods: {

        changePage(page) {
            this.updateJobs(page);
        },
        changePageSize(pageSize) {
            this.updateJobs(1, pageSize);
        },

        onChangeServiceFilter() {
            this.updateJobs(1);
        },

        fmt(d) {
            if (!d) {
                return '';
            }

            return moment(d).locale(BEDITA.locale.slice(0, 2)).format('D MMM YYYY kk:mm');
        },

        toggleOpen() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.updateJobs(this.pagination?.page || 1);
            }
        },

        togglePayload(jobId) {
            if (this.showPayloadId == jobId) {
                this.showPayloadId = null;

                return;
            }

            this.showPayloadId = jobId;
        },

        updateJobs(page = 1, pageSize = this.pagination?.page_size) {
            if (!this.isOpen) {
                return;
            }
            let query = `page=${page}&page_size=${pageSize}`;
            const serviceFilter = this.service !== 'all' ? this.service : this.selectedService;
            if (serviceFilter && serviceFilter !== 'all') {
                query += `&service=${serviceFilter}`;
            }
            let requestUrl = `${BEDITA.base}/admin/async_jobs/jobs?${query}`;
            const options =  {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };
            this.loading = true;

            return fetch(requestUrl, options)
                .then((response) => response.json())
                .then((json) => {
                    if (json.jobs) {
                        this.jobs = json.jobs;
                        this.pagination = json.pagination;

                        return this.jobs;
                    }
                })
                .catch((error) => {
                    console.error(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
}
</script>
<style scoped>
.admin-job-list {
    max-width: 1200px;
}
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.service-filter {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.service-filter select {
    min-width: 220px;
}
.paginationContainer {
    display: flex;
    justify-content: flex-end;
    margin-left: auto;
}
#list-jobs {
    max-width: 1200px;
    display: grid;
    grid-template-columns: 1fr 200px 200px 200px 100px 50px;
}
#list-jobs.file-column {
    grid-template-columns: 1fr 200px 200px 200px 200px 100px 50px;
}
#list-jobs > div {
    border-bottom: 1px solid gray;
}
.row-hover {
    background-color: #414141;
}
.job-payload {
    grid-column: span 6 !important;
}
.job-payload-file {
    grid-column: span 7 !important;
}
</style>
