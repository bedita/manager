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
                        @change="updateJobs(1)"
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
                    <input
                        :placeholder="msgOtherService"
                        type="text"
                        v-model="otherService"
                        @input="debounceFilterUpdate"
                    >
                </div>
                <div>
                    <input
                        :placeholder="msgUuid"
                        class="uuid-filter"
                        type="text"
                        v-model="uuidFilter"
                        @input="debounceFilterUpdate"
                    >
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
                <div id="list-jobs">
                    <div
                        v-for="job in jobs"
                        :key="job.id"
                        class="job-row"
                    >
                        <div class="job-summary">
                            <div class="job-summary-primary">
                                <div class="job-identifier-cell">
                                    <span
                                        class="job-identifier"
                                        :title="job.id"
                                    >
                                        {{ job.id }}
                                    </span>
                                </div>
                                <div class="job-status-cell">
                                    <span
                                        class="job-status"
                                        :class="{
                                            'job-status-failed': job.meta.status === 'failed',
                                            'job-status-completed': job.meta.status === 'completed',
                                            'job-status-pending': job.meta.status === 'pending',
                                        }"
                                    >
                                        {{ job.meta.status }}
                                    </span>
                                </div>
                                <div class="job-actions-cell">
                                    <button
                                        type="button"
                                        class="payload-toggle"
                                        :class="showPayloadId != job.id ? 'icon-plus' : 'icon-minus'"
                                        :aria-label="showPayloadId == job.id ? msgHideJobDetails : msgShowJobDetails"
                                        :aria-expanded="showPayloadId == job.id"
                                        :aria-controls="`job-details-${job.id}`"
                                        :title="showPayloadId == job.id ? msgHideJobDetails : msgShowJobDetails"
                                        @click="togglePayload(job.id)"
                                    />
                                </div>
                            </div>
                            <div class="job-summary-secondary">
                                <div class="job-service-cell">
                                    <span class="mobile-field-label">
                                        {{ msgService }}:
                                    </span>
                                    {{ job.attributes.service }}
                                </div>
                                <div class="job-created-cell">
                                    <span class="mobile-field-label">
                                        {{ msgCreatedOn }}:
                                    </span>
                                    {{ fmt(job.meta.created) }}
                                </div>
                            </div>
                        </div>
                        <div
                            :id="`job-details-${job.id}`"
                            class="job-payload"
                            v-show="showPayloadId == job.id"
                        >
                            <dl class="job-details-metadata">
                                <div v-if="columnFile">
                                    <dt>{{ msgFileName }}</dt>
                                    <dd
                                        class="job-filename"
                                        :title="job.attributes.payload && job.attributes.payload.filename"
                                    >
                                        {{ job.attributes.payload && job.attributes.payload.filename }}
                                    </dd>
                                </div>
                                <div v-if="job.meta.completed">
                                    <dt>{{ msgCompletedOn }}</dt>
                                    <dd>{{ fmt(job.meta.completed) }}</dd>
                                </div>
                                <div v-if="job.attributes.scheduled_from">
                                    <dt>{{ msgScheduledFrom }}</dt>
                                    <dd>{{ fmt(job.attributes.scheduled_from) }}</dd>
                                </div>
                                <div v-if="job.attributes.expires">
                                    <dt>{{ msgExpires }}</dt>
                                    <dd>{{ fmt(job.attributes.expires) }}</dd>
                                </div>
                                <div>
                                    <dt>{{ msgMaxAttempts }}</dt>
                                    <dd>{{ job.attributes.max_attempts }}</dd>
                                </div>
                            </dl>
                            <section class="job-json-section">
                                <div class="job-json-heading">
                                    <h3>{{ msgPayload }}</h3>
                                    <clipboard-item
                                        :label="msgCopy"
                                        :text="formatJson(job.attributes.payload)"
                                    />
                                </div>
                                <div
                                    :id="`container-payload-${job.id}`"
                                    class="job-json-panel"
                                />
                                <json-editor
                                    :options="jsonEditorOptions"
                                    :target="`container-payload-${job.id}`"
                                    :text="formatJson(job.attributes.payload)"
                                />
                            </section>

                            <section
                                class="job-json-section"
                                v-if="job.attributes.results"
                            >
                                <div class="job-json-heading">
                                    <h3>{{ msgResults }}</h3>
                                    <clipboard-item
                                        :label="msgCopy"
                                        :text="formatJson(job.attributes.results)"
                                    />
                                </div>
                                <div
                                    :id="`container-results-${job.id}`"
                                    class="job-json-panel"
                                />
                                <json-editor
                                    :options="jsonEditorOptions"
                                    :target="`container-results-${job.id}`"
                                    :text="formatJson(job.attributes.results)"
                                />
                            </section>
                        </div>
                    </div>
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
        queryFilter: {
            type: Object,
            default: () => ({}),
        },
        service: {
            type: String,
            default: () => 'all',
        },
    },

    data() {
        return {
            columnFile: false,
            filterDebounceTimer: null,
            isOpen: false,
            jobs: [],
            jsonEditorOptions: {
                mainMenuBar: false,
            },
            loading: false,
            otherService: '',
            pagination: {
                page: 1,
                page_size: 20,
                page_count: 1,
                total_count: 0,
            },
            hoveredJobId: null,
            selectedService: 'all',
            serviceOptions: ['credentials_change', 'mail', 'signup', 'thumbnail'],
            showPayloadId: null,
            uuidFilter: '',
            msgAll: t`All`,
            msgAsyncJobs: t`Async Jobs`,
            msgCreatedOn: t`Created on`,
            msgCompletedOn: t`Completed on`,
            msgCopy: t`Copy`,
            msgExpires: t`Expires`,
            msgFileName: t`File name`,
            msgJob: t`Job`,
            msgJobs: t`Jobs`,
            msgMaxAttempts: t`Max attempts`,
            msgNoJobs: t`No Jobs`,
            msgOtherService: t`Other service`,
            msgPayload: t`Payload`,
            msgResults: t`Results`,
            msgScheduledFrom: t`Scheduled from`,
            msgService: t`Service`,
            msgShowJobDetails: t`Show job details`,
            msgStatus: t`Status`,
            msgUuid: t`UUID`,
            msgHideJobDetails: t`Hide job details`,
        };
    },

    async mounted() {
        this.columnFile = !['all', 'credentials_change', 'mail', 'signup', 'thumbnail'].includes(this.service);
        this.selectedService = this.service;
        this.$nextTick(async () => {
            this.toggleOpen();
        });
        this.uuidFilter = this.queryFilter?.uuid || '';
    },

    beforeDestroy() {
        clearTimeout(this.filterDebounceTimer);
    },

    methods: {

        debounceFilterUpdate() {
            clearTimeout(this.filterDebounceTimer);
            this.filterDebounceTimer = setTimeout(() => {
                this.updateJobs(1);
            }, 300);
        },

        changePage(page) {
            this.updateJobs(page);
        },

        changePageSize(pageSize) {
            this.updateJobs(1, pageSize);
        },

        fmt(d) {
            if (!d) {
                return '';
            }

            return moment(d).locale(BEDITA.locale.slice(0, 2)).format('D MMM YYYY kk:mm');
        },

        formatJson(value) {
            const formatted = JSON.stringify(value, null, 2);

            return formatted === undefined ? 'null' : formatted;
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

        updateJobs(page = 1, pageSize = this.pagination?.page_size, uuid = this.uuidFilter) {
            if (!this.isOpen) {
                return;
            }
            let query = `page=${page}&page_size=${pageSize}`;
            const serviceFilter = this.service !== 'all' ? this.service : this.selectedService;
            if (serviceFilter && serviceFilter !== 'all') {
                query += `&filter[service]=${serviceFilter}`;
            }
            if (this.otherService) {
                query += `&filter[service]=${this.otherService}`;
            }
            if (uuid) {
                query += `&filter[uuid]=${uuid}`;
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
    max-width: 1500px;
}
.toolbar {
    display: flex;
    flex-wrap: wrap;
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
.uuid-filter {
    min-width: 300px;
}
.paginationContainer {
    display: flex;
    justify-content: flex-end;
    margin-left: auto;
}
#list-jobs {
    display: flex;
    flex-direction: column;
    max-width: 1500px;
}
.job-row {
    width: 100%;
    padding: 0.75rem;
    border-bottom: 1px solid rgba(128, 128, 128, 0.35);
    box-sizing: border-box;
}
.job-row:nth-child(even) {
    background-color: rgba(128, 128, 128, 0.12);
}
.tab-container {
    max-width: 100%;
}
.job-identifier-cell {
    flex: 1 1 auto;
    min-width: 0;
}
.job-summary-primary {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.job-identifier {
    display: block;
    overflow: hidden;
    font-family: monospace;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.job-summary-secondary {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem 1rem;
    margin-top: 0.35rem;
}
.job-service-cell,
.job-created-cell {
    min-width: 0;
    overflow-wrap: anywhere;
}
.job-status-cell,
.job-actions-cell {
    flex: 0 0 auto;
}
.job-actions-cell {
    margin-left: auto;
}
.mobile-field-label {
    font-weight: 600;
}
.job-details-metadata {
    display: block;
    margin: 0 0 1rem;
}
.job-details-metadata > div {
    margin-bottom: 0.5rem;
}
.job-details-metadata dt {
    font-weight: 600;
}
.job-details-metadata dd {
    margin: 0.15rem 0 0;
    overflow-wrap: anywhere;
}
.job-filename {
    overflow: hidden;
    text-overflow: ellipsis;
    overflow-wrap: anywhere;
}
.row-hover {
    background-color: #414141;
}
.job-status {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    border: 1px solid #b8bec7;
    border-radius: 999px;
    background-color: #e6e8eb;
    color: #252a31;
    font-size: 0.875em;
    font-weight: 600;
    line-height: 1.4;
    white-space: nowrap;
}
.job-status-failed {
    border-color: #dc3545;
    background-color: #f8d7da;
    color: #842029;
}
.job-status-completed {
    border-color: #198754;
    background-color: #d1e7dd;
    color: #0f5132;
}
.job-status-pending {
    border-color: #ffc107;
    background-color: #fff3cd;
    color: #664d03;
}
.payload-toggle {
    padding: 0.25rem;
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
}
.payload-toggle:focus-visible {
    outline: 2px solid currentColor;
    outline-offset: 2px;
}
.job-payload {
    width: 100%;
    min-width: 0;
    padding-top: 0.75rem;
    border-top: 1px solid #b8bec7;
    box-sizing: border-box;
}
.job-json-section + .job-json-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(128, 128, 128, 0.35);
}
.job-json-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.5rem;
}
.job-json-heading h3 {
    margin: 0;
    font-size: 1rem;
}
.job-json-panel {
    max-height: 320px;
    overflow: auto;
    border: 1px solid rgba(128, 128, 128, 0.25);
    border-radius: 3px;
    padding: 0.5rem 0.75rem;
}
@media (max-width: 768px) {
    .toolbar {
        flex-direction: column;
        align-items: stretch;
        margin-left: 0 !important;
    }
    .service-filter {
        flex-direction: column;
        align-items: stretch;
    }
    .service-filter select,
    .service-filter input,
    .toolbar > div > .uuid-filter {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }
    .paginationContainer {
        justify-content: flex-start;
        flex-wrap: wrap;
        margin-left: 0;
    }
}
</style>
