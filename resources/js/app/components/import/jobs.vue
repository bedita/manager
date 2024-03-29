<template>
    <div class="columns">
        <div class="column">
            <section class="fieldset">
                <header>
                    <h2>{{ msgJobs }}</h2>
                </header>
                <div class="is-loading-spinner mt-05" v-if="loading"></div>
                <div class="tab-container" v-if="!loading && jobs.length > 0">
                    <div id="list-jobs">
                        <div class="table-header">Job ID</div>
                        <div class="table-header">{{ msgFileName }}</div>
                        <div class="table-header">{{ msgServiceName }}</div>
                        <div class="table-header">{{ msgScheduledFrom }}</div>
                        <div class="table-header">{{ msgCompletedOn }}</div>
                        <div class="table-header">{{ msgStatus }}</div>
                        <div class="table-header"></div>
                        <template v-for="job in currentJobs">
                            <div :class="job.meta.status">{{ job.id }}</div>
                            <div :class="job.meta.status">{{ job.attributes.payload && job.attributes.payload.filename }}</div>
                            <div :class="job.meta.status">{{ job.attributes.service }}</div>
                            <div :class="job.meta.status">{{ fmt(job.attributes.scheduled_from) }}</div>
                            <div :class="job.meta.status">{{ fmt(job.meta.completed) }}</div>
                            <div :class="job.meta.status">{{ job.meta.status }}</div>
                            <div :class="job.meta.status">
                                <a :class="showPayloadId != job.id ? 'icon-plus' : 'icon-minus'" v-on:click.prevent="togglePayload(job.id)"></a>
                            </div>
                            <div class="job-payload" v-show="showPayloadId == job.id">
                                <h3>Payload</h3>
                                <div :id="`container-payload-${job.id}`"></div>
                                <json-editor
                                    :options="jsonEditorOptions"
                                    :target="`container-payload-${job.id}`"
                                    :text="JSON.stringify(job.attributes.payload)">
                                </json-editor>

                                <h3 v-if="job.attributes.results">Results</h3>
                                <div v-if="job.attributes.results" :id="`container-results-${job.id}`"></div>
                                <json-editor v-if="job.attributes.results"
                                    :options="jsonEditorOptions"
                                    :target="`container-results-${job.id}`"
                                    :text="JSON.stringify(job.attributes.results)">
                                </json-editor>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="mt-05" v-if="!loading && jobs.length === 0">{{ msgNoJobs }}</div>
            </section>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';

export default {
    name: 'jobs-index',

    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
    },

    props: {
        jobs: {
            type: Array,
            default: () => ([]),
        },
        services: {
            type: Array,
            default: () => ([]),
        },
        timeout: {
            type: Number,
            default: 30000,
        }
    },

    data() {
        return {
            currentJobs: () => [],
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

    created() {
        this.currentJobs = this.jobs;
    },

    mounted() {
        if (this.services.length) {
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

        togglePayload(jobId) {
            if (this.showPayloadId == jobId) {
                this.showPayloadId = null;

                return;
            }

            this.showPayloadId = jobId;
        },

        updateJobs() {
            const requestUrl = `${BEDITA.base}/import/jobs`;
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
                        this.currentJobs = json.jobs;

                        return this.currentJobs;
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
