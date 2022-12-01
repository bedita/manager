/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Import/index.twig
 *
 * <modules-view> component used for ModulesPage -> View
 */

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
            this.fileName = '';
            if (this.$helpers.checkMaxFileSize(e.target.files[0]) === false) {
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
