/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Import/index.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */
import { warning } from 'app/components/dialog/dialog';
import { t } from 'ttag';

export default {
    props: {
        jobs: {
            type: Array,
            default: () => [],
        },
        services: {
            type: Array,
            default: () => [],
        },
        timeout: {
            type: Number,
            default: 5000,
        }
    },

    data() {
        return {
            fileName: '',
            currentJobs: () => [],
            showPayloadId: null,
            currentFilterId: null,
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
        onFileChanged(e) {
            const fileSize = Math.round(e.target.files[0].size);
            const maxFileSize = BEDITA.maxFileSize;
            if (fileSize >= maxFileSize) {
                this.fileName = '';
                const fileSizeMb = Math.round(fileSize / 1024 / 1024);
                const maxFileSizeMb = Math.round(BEDITA.maxFileSize / 1024 / 1024);
                const message = t`File too big (${fileSizeMb} MB), please select a file less than max file size (${maxFileSizeMb} MB)`;
                warning(message);

                return;
            }
            this.fileName = e.target.files[0].name;
        },

        /**
         * Update jobs.
         */
        updateJobs() {
            let requestUrl = `${BEDITA.base}/import/jobs`;

            const options =  {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };

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
                });
        },

        /**
         * Toggle job payload to show
         * @param {int} jobId The job id
         */
        togglePayload(jobId) {
            if (this.showPayloadId == jobId) {
                this.showPayloadId = null;

                return;
            }

            this.showPayloadId = jobId;
        }
    }
}
