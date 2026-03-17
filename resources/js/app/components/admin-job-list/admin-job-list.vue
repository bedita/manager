<template>
    <div class="columns">
        <div class="column">
            <section class="fieldset">
                <details>
                    <summary @click="toggleOpen" :open="isOpen">{{ tr(service) }}</summary>
                    <div>
                        <div
                            class="is-loading-spinner mt-05"
                            v-if="loading"
                        />
                        <div
                            class="tab-container"
                            v-if="!loading && jobs?.length > 0"
                        >
                            <div>
                                Page
                                <template v-if="pagination.page > 1">
                                    <a @click="updateJobs(pagination.page - 1)"><app-icon icon="carbon:chevron-left" /></a>
                                </template>
                                {{ pagination.page }} / {{ pagination.page_count }}
                                <template v-if="pagination.page < pagination.page_count">
                                    <a @click="updateJobs(pagination.page + 1)"><app-icon icon="carbon:chevron-right" /></a>
                                </template>
                            </div>
                            <div id="list-jobs" :class="{ 'file-column': columnFile }">
                                <div class="table-header">
                                    Job
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
                                    <div :class="job.meta.status">
                                        {{ fmt(job.meta.created) }}
                                        <br />
                                        {{ job.id }}
                                    </div>
                                    <div :class="job.meta.status" v-if="columnFile">
                                        {{ job.attributes.payload && job.attributes.payload.filename }}
                                    </div>
                                    <div :class="job.meta.status">
                                        {{ job.attributes.service }}
                                    </div>
                                    <div :class="job.meta.status">
                                        {{ fmt(job.attributes.scheduled_from) }}
                                    </div>
                                    <div :class="job.meta.status">
                                        {{ fmt(job.meta.completed) }}
                                    </div>
                                    <div :class="job.meta.status">
                                        {{ job.meta.status }}
                                    </div>
                                    <div :class="job.meta.status">
                                        <a :class="showPayloadId != job.id ? 'icon-plus' : 'icon-minus'"
                                            @click.prevent="togglePayload(job.id)"
                                        />
                                    </div>
                                    <div
                                        class="job-payload" :class="{ 'job-payload-file': columnFile }"
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
                </details>
            </section>
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
            pagination: {},
            showPayloadId: null,
            msgCompletedOn: t`Completed on`,
            msgFileName: t`File name`,
            msgJobs: t`Jobs`,
            msgNoJobs: t`No Jobs`,
            msgScheduledFrom: t`Scheduled from`,
            msgServiceName: t`Service name`,
            msgStatus: t`Status`,
        };
    },

    mounted() {
        this.columnFile = !['all', 'credentials_change', 'mail', 'signup', 'thumbnail'].includes(this.service);
    },

    methods: {

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

        tr(key) {
            return t`${key}`;
        },

        updateJobs(page = 1) {
            if (!this.isOpen) {
                return;
            }
            let query = `page=${page}`;
            if (this.service !== 'all') {
                query += `&service=${this.service}`;
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
#list-jobs {
    max-width: 1400px;
    display: grid;
    grid-template-columns: 400px 200px 200px 200px 100px 50px;
}
#list-jobs.file-column {
    grid-template-columns: 400px 200px 200px 200px 200px 100px 50px;
}
.job-payload {
    grid-column: span 6 !important;
}
.job-payload-file {
    grid-column: span 7 !important;
}
</style>
