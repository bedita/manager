<template>
    <div class="file-upload">
        <header
            :class="`mb-2 mt-2 tab unselectable open has-border-module-${objectType}`"
            v-if="objectId"
        >
            <h3>{{ msgReplaceMedia }}</h3>
        </header>
        <div class="fieldset">
            <div class="file has-name">
                <label class="file-label">
                    <input
                        type="file"
                        class="file-input"
                        name="file"
                        :accept="fileAcceptMimeTypes(objectType)"
                        @change="onFileChange"
                    >
                    <span class="file-cta">
                        <app-icon icon="carbon:upload" />
                        <span class="ml-05">{{ msgChooseFile }}</span>
                    </span>
                    <span class="file-name">
                        <span :data-empty-label="msgNoFileSelected">
                            {{ fileName }}
                        </span>
                    </span>
                </label>
            </div>
        </div>
        <div>
            <button
                class="button button-primary"
                :class="loading ? 'is-loading-spinner' : 'button button-primary'"
                :disabled="readonly || loading"
                @click.prevent="upload()"
                v-if="fileName && Object.keys(error).length === 0"
            >
                <template v-if="!loading">
                    <app-icon icon="carbon:upload" />
                    <span class="ml-05">{{ msgFileUpload }}</span>
                </template>
                <span v-if="loading && loadingMessage">{{ loadingMessage }}</span>
            </button>
        </div>

        <div class="progress is-flex is-flex-column">
            <div
                class="upload-item"
                v-for="(info, index) in Array.from(uploadProgressInfo.values())"
                :key="index"
            >
                <div class="progress-bar">
                    <div
                        class="progress-bar-status"
                        :class="progressBarClass(info)"
                        :style="progressBarStyle(info)"
                    />
                </div>
                <div class="message">
                    <span v-show="!info.error && !info.cancelled && !info.done">{{ info?.progress }}%</span>
                    <span v-show="info.error && !info.cancelled && !info.done">{{ info?.errorMsg }}</span>
                    <span
                        class="has-text-gray-500"
                        v-show="(info.error || info.cancelled) && !info.done"
                    >
                        {{ msgCancelled }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { FetchMixin } from 'app/mixins/fetch';
import { t } from 'ttag';

export default {
    name: 'FileUpload',
    mixins: [ FetchMixin ],
    inject: ['getCSFRToken'],
    props: {
        objectFormReference: {
            type: String,
            default: '',
        },
        objectId: {
            type: [ String, null],
            default: null,
        },
        objectType: {
            type: String,
            required: true,
        },
        readonly: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            axiosCancelTokens: new Map(),
            error: {},
            fileData: null,
            fileName: '',
            loading: false,
            msgCancelled: t`Canceled`,
            msgChooseFile: t`Choose file`,
            msgFileUpload: t`File upload`,
            msgNoFileSelected: t`No file selected`,
            msgReplaceMedia: t`Replace media`,
            msgUploading: t`Uploading...`,
            msgUploadSucceeded: t`Upload succeeded`,
            response: {},
            uploadProgressInfo: new Map(),
        };
    },

    methods: {

        fileAcceptMimeTypes(type) {
            return this.$helpers.acceptMimeTypes(type);
        },

        async onFileChange(e) {
            this.error = {};
            this.fileData = null;
            this.fileName = '';
            const fileData = e?.target?.files?.[0] || null;
            if (!fileData) {
                return;
            }
            if (this.$helpers.checkMimeForUpload(fileData, this.objectType) === false) {
                return;
            }
            if (this.objectType === 'images') {
                this.$helpers.checkImageResolution(fileData);
            }
            if (this.$helpers.checkMaxFileSize(fileData) === false) {
                return false;
            }
            this.fileData = fileData;
            this.fileName = this.fileData.name;
            this.$forceUpdate();
        },

        progressBarClass(info) {
            return {
                'done': info?.done,
                'error': info?.error,
                'pending': info?.pending,
                'in-progress': !info?.done && !info?.error && info?.progress,
                'cancelled': info?.cancelled
            };
        },

        progressBarStyle(info) {
            return { width: `${info?.progress}%` };
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

        async upload() {
            try {
                this.loading = true;
                this.loadingMessage = this.msgUploading;
                const result = await this.doUpload();
                this.$emit('success', result);
                this.loadingMessage = this.msgUploadSucceeded;
            } catch(e) {
                this.$emit('error', e);
            } finally {
                this.loading = false;
            }
        },

        async doUpload() {
            const data = {
                'title': this.$helpers.titleFromFileName(this.fileData?.name || ''),
                'status': 'on',
                'model-type': this.objectType,
                'file': this.fileData,
            };
            if (this.objectFormReference) {
                const prefix = `fast-${this.objectType}-`;
                const objectData = document.querySelectorAll(this.objectFormReference);
                for (const element of objectData) {
                    if (['radio', 'checkbox'].includes(element?.type) && !element?.checked) {
                        continue;
                    }
                    data[element.name.replace(prefix, '')] = element.value;
                }
            }
            data['name'] = this.fileData.name;
            const formData = new FormData();
            for (const key of Object.keys(data)) {
                formData.append(key, data[key]);
            }
            const url = `/${this.objectType}/save`;
            const CancelToken = this.getAxios().CancelToken;
            const source = CancelToken.source();
            this.axiosCancelTokens.set(this.fileData, source);
            const config = {
                onUploadProgress: (progressEvent) => {
                    const progress = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                    this.setProgressInfo(this.fileData, progress);
                },
                onUploadCancelled: () => this.setProgressInfo(this.fileData, -1, true, false, false, 'Upload Cancelled'),
                onUploadError: (err) => this.setProgressInfo(this.fileData, 100, false, true, false, `Error from server: ${err.message ? err.message : ''}`),
                onUploadSuccess: () => this.setProgressInfo(this.fileData, 100, false, false, true),
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
    },
}
</script>
<style scoped>
.file-upload .tab h3 {
    flex-grow: 1;
    margin: 0;
    padding: 0 16px 0 0;
}
.file-upload .file {
    user-select: none;
    align-items: stretch;
    display: flex;
    justify-content: flex-start;
    position: relative;
    max-width: fit-content;
}
.file-upload .loading:before {
    animation: spinAround .5s infinite linear;
    border: 2px solid #dbdbdb;
    border-radius: 290486px;
    border-right-color: rgba(0, 0, 0, 0);
    border-top-color: rgba(0, 0, 0, 0);
    content: "";
    display: inline-block;
    height: 16px;
    position: relative;
    width: 16px;
}
</style>
