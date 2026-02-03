<template>
    <div class="columns">
        <div class="column">
            <section class="fieldset">
                <details>
                    <summary @click="toggleOpen" :open="isOpen">{{ msgJobs }} {{ service }}</summary>
                    <div>
                        <div
                            class="is-loading-spinner mt-05"
                            v-if="loading"
                        />
                        <div
                            class="tab-container"
                            v-if="!loading && jobs?.length > 0"
                        >
                            <div id="list-jobs">
                                <div class="table-header">
                                    Job ID
                                </div>
                                <div class="table-header">
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
                                        {{ job.id }}
                                    </div>
                                    <div :class="job.meta.status">
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
                                        class="job-payload"
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
    name: 'ImportJobList',

    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
    },

    props: {
        service: {
            type: String,
            default: () => '',
        },
        timeout: {
            type: Number,
            default: 30000,
        }
    },

    data() {
        return {
            isOpen: false,
            jobs: [],
            jsonEditorOptions: {
                mainMenuBar: false,
            },
            loading: false,
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
        if (this.service.length) {
            setInterval(() => {
                this.updateJobs();
            }, this.timeout);
        }
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
                this.updateJobs();
            }
        },

        togglePayload(jobId) {
            if (this.showPayloadId == jobId) {
                this.showPayloadId = null;

                return;
            }

            this.showPayloadId = jobId;
        },

        updateJobs() {
            if (!this.isOpen) {
                return;
            }
            const requestUrl = `${BEDITA.base}/import/jobs?service=${this.service}`;
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
