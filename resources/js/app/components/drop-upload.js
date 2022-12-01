/**
 * View component used for uploading files and create media media object
 *
 * @property {FileList} files
 *
 */

import { FetchMixin } from 'app/mixins/fetch';
import { t } from 'ttag';

export default {
    mixins: [ FetchMixin ],
    inject: ['getCSFRToken'],

    template: `
        <div class="drop-area mb-1 p-1 is-flex is-flex-column"
            @drop.prevent="dropFiles"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave">

            <div class="upload-placeholder" v-if="!Array.from(uploadProgressInfo.values()).length">
                <input class="file-input" type="file" multiple @change="inputFiles">
                <: placeholder :>
            </div>

            <div class="upload-items is-flex is-flex-column" v-else>
                <div class="upload-item"
                    :key="index"
                    v-for="(info, index) in Array.from(uploadProgressInfo.values())">

                    <div class="upload-item-header" :class="{'is-loading-spinner': info.pending }">
                        <span class="name" :class="info.cancelled? 'has-text-gray-500' : ''"><: info.file.name :></span>

                        <button v-show="!info.error && !info.cancelled && !info.done && !info.pending"
                            class="button-outlined icon-stop"
                            @click.stop.prevent="abortUpload(info.file)">${t`stop`}</button>

                        <button v-show="(info.error || info.cancelled) && !info.done"
                            class="button-outlined icon-cancel"
                            @click.stop.prevent="removeProgressItem(info.file)">${t`remove`}</button>

                        <span v-show="!info.error && !info.cancelled && info.done" class="icon-ok"></span>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-bar-status" :class="progressBarClass(info)" :style="progressBarStyle(info)"></div>
                    </div>

                    <div class="message">
                        <span v-show="!info.error && !info.cancelled && info.done">${t`done`}</span>
                        <span v-show="!info.error && !info.cancelled && !info.done"><: info.progress :>%</span>
                        <span v-show="info.error && !info.cancelled && !info.done"><: info.errorMsg :></span>
                        <span v-show="(info.error || info.cancelled) && !info.done" class="has-text-gray-500">${t`Cancelled`}</span>
                    </div>
                </div>
            </div>
        </div>
    `,

    props: {
        placeholder: {
            type: String,
            default: () => '',
        },
        knownTypes: {
            type: Array,
            default: () => ['audio', 'video', 'image'],
        },
    },

    data() {
        return {
            uploadProgressInfo: new Map(), // real-time upload status
            axiosCancelTokens: new Map(), // Map of all axios cancel token
        }
    },

    methods: {
        inputFiles(e) {
            const files = e.target.files || null;
            if (!files) {
                return;
            }

            this.uploadFiles(files);
        },

        dropFiles(e) {
            const files = e.dataTransfer.files || null;
            if (!files) {
                return;
            }

            this.uploadFiles(files);
        },

        uploadFiles(files) {
            this.$el.classList.remove('dragover');
            ([...files]).forEach(f => {
                if (this.$helpers.checkMaxFileSize(f) === false) {
                    return;
                }
                this.upload(f)
                    .then((object) => {
                        this.$emit('new-relations', [object]);
                        this.removeProgressItem(f);
                    });

            });
        },

        onDragOver() {
            this.$el.classList.add('dragover');
        },

        onDragLeave() {
            this.$el.classList.remove('dragover');
        },

        // return promise
        upload(file) {
            const objectType = this.getObjectType(file);
            const formData = new FormData();
            formData.append('title', file.name);
            formData.append('status', 'on');
            formData.append('file', file);
            formData.append('model-type', objectType);

            const url = `/${objectType}/save`;

            const CancelToken = this.getAxios().CancelToken;
            const source = CancelToken.source();
            this.axiosCancelTokens.set(file, source);

            const config = {
                onUploadProgress: (progressEvent) => {
                    const progress = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                    this.setProgressInfo(file, progress);
                },
                onUploadCancelled: () => this.setProgressInfo(file, -1, true, false, false, 'Upload Cancelled'),
                onUploadError: (err) => this.setProgressInfo(file, 100, false, true, false, `Error from server: ${err.message ? err.message : ''}`),
                onUploadSuccess: () => this.setProgressInfo(file, 100, false, false, true),
                cancelToken: source.token,
            };

            return this.axios.post(url, formData, config)
                .then(response => {
                    let data = response.data && response.data.data;
                    if (data) {
                        if (Array.isArray(data)) {
                            data = data[0];
                        }
                        config.onUploadSuccess(data);
                        return Promise.resolve(data);
                    }
                    return Promise.reject(response);
                })
                .catch((error) => {
                    if (this.getAxios().isCancel(error)) {
                        config.onUploadCancelled();
                        return Promise.reject(error);
                    }
                    config.onUploadError(error);
                    return Promise.reject(error);
                });
        },

        setProgressInfo(file, progress = 0, cancelled = false, error = false, done = false, errorMsg = '') {
            const pending = !done && !cancelled && !error && progress === 100;

            if (progress < 0) {
                if (this.uploadProgressInfo.has(file)) {
                    const info = this.uploadProgressInfo.get(file);
                    progress = info.progress;
                }
            }

            this.uploadProgressInfo.set(file, {
                file,
                progress,
                cancelled,
                pending,
                done,
                error,
                errorMsg,
            });

            // force vue to render (an object Map is not reactive in vue)
            this.$forceUpdate();
        },

        getObjectType(file) {
            let type = file.type && file.type.split('/')[0];
            const hasPlural = /audio/g.test(type) ? '' : 's';

            if (this.knownTypes.indexOf(type) === -1) {
                type = 'file';
            }
            return `${type}${hasPlural}`;
        },

        progressBarClass(info) {
            return {
                'done': info.done,
                'error': info.error,
                'pending': info.pending,
                'in-progress': !info.done && !info.error && info.progress,
                'cancelled': info.cancelled
            };
        },

        progressBarStyle(info) {
            return { width: `${info.progress}%` };
        },

        abortUpload(file) {
            if (this.axiosCancelTokens.get(file)) {
                this.axiosCancelTokens.get(file).cancel(t('Operation canceled by the user'));
            }
        },

        removeProgressItem(file) {
            this.uploadProgressInfo.delete(file);
            this.$forceUpdate();
        },
    },
}
