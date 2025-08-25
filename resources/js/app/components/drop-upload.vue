<template>
    <div class="drop-upload drop-area mb-1 p-1 is-flex is-flex-column" :class="double ? 'drop-area-double' : ''" @drop.prevent="dropFiles" @dragover.prevent="onDragOver"
        @dragleave.prevent="onDragLeave">
        <div class="upload-placeholder" v-if="!Array.from(uploadProgressInfo.values()).length">
            <input class="file-input" type="file" multiple @change="inputFiles" :accept="fileAcceptMimeTypes(objectType)">
            {{ placeholder }}
        </div>

        <div class="upload-items is-flex is-flex-column" v-else>
            <div class="upload-item" v-for="(info, index) in Array.from(uploadProgressInfo.values())" :key="index">
                <div class="upload-item-header" :class="{ 'is-loading-spinner': info.pending }">
                    <span class="name" :class="info.cancelled ? 'has-text-gray-500' : ''">
                        {{ info?.file?.name }}
                    </span>
                    <button
                        v-show="!info.error && !info.cancelled && !info.done && !info.pending"
                        class="button-outlined"
                        @click.stop.prevent="abortUpload(info.file)"
                    >
                        <app-icon icon="carbon:stop"></app-icon>
                        <span class="ml-05">{{ msgStop }}</span>
                    </button>
                    <button
                        v-show="(info.error || info.cancelled) && !info.done"
                        class="button-outlined"
                        @click.stop.prevent="removeProgressItem(info.file)"
                    >
                        <app-icon icon="carbon:trash-can"></app-icon>
                        <span class="ml-05">{{ msgRemove }}</span>
                    </button>
                    <span
                        v-show="!info.error && !info.cancelled && info.done"
                        class="icon-ok"
                    >
                    </span>
                </div>

                <div class="progress-bar">
                    <div class="progress-bar-status" :class="progressBarClass(info)" :style="progressBarStyle(info)"></div>
                </div>

                <div class="message">
                    <span v-show="!info.error && !info.cancelled && info.done">{{ msgDone }}</span>
                    <span v-show="!info.error && !info.cancelled && !info.done">
                        {{ info?.progress }}%
                    </span>
                    <span v-show="info.error && !info.cancelled && !info.done">
                        {{ info?.errorMsg }}
                    </span>
                    <span v-show="(info.error || info.cancelled) && !info.done" class="has-text-gray-500">
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
    name: 'DropUpload',
    mixins: [ FetchMixin ],
    inject: ['getCSFRToken'],
    props: {
        placeholder: {
            type: String,
            default: () => '',
        },
        knownTypes: {
            type: Array,
            default: () => ['audio', 'video', 'image'],
        },
        double: {
            type: String,
            default: '',
        },
        objectType: {
            type: String,
            default: 'media',
        },
    },

    data() {
        return {
            uploadProgressInfo: new Map(), // real-time upload status
            axiosCancelTokens: new Map(), // Map of all axios cancel token
            msgCancelled: t`Cancelled`,
            msgDone: t`Done`,
            msgRemove: t`Remove`,
            msgStop: t`Stop`,
        }
    },

    methods: {
        fileAcceptMimeTypes(type) {
            return this.$helpers.acceptMimeTypes(type);
        },

        inputFiles(e) {
            if (this.$helpers.checkMimeForUpload(e.target.files[0], this.objectType) === false) {
                return;
            }
            if (this.objectType === 'images') {
                this.$helpers.checkImageResolution(e.target.files[0]);
            }
            if (this.$helpers.checkMaxFileSize(e.target.files[0]) === false) {
                return false;
            }

            const files = e?.target?.files || null;
            if (!files) {
                return;
            }
            this.uploadFiles(files);
        },

        dropFiles(e) {
            const files = e?.dataTransfer?.files || null;
            if (!files) {
                return;
            }
            const toProcess = [];
            for (const file of files) {
                if (this.$helpers.checkMimeForUpload(file, this.objectType) === false) {
                    continue;
                }
                if (this.objectType === 'images') {
                    this.$helpers.checkImageResolution(file);
                }
                if (this.$helpers.checkMaxFileSize(file) === false) {
                    continue;
                }
                toProcess.push(file);
            }
            this.uploadFiles(toProcess);
        },

        async uploadFile(file) {
            try {
                const object = await this.upload(file);
                this.uploadSuccessful(file, object);
            } catch (error) {
                console.error('upload error', error);
            }
        },

        async uploadFiles(files) {
            this.$el.classList.remove('dragover');
            // show all files in view
            [...files].forEach(file => this.setProgressInfo(file));
            const [uniqueFiles, duplicatesFiles] = this.filterFiles([...files]);
            for (const file of uniqueFiles) {
                // skip file not allowed by size and remove it from view
                if (this.$helpers.checkMaxFileSize(file) === false) {
                    this.removeProgressItem(file);
                    return;
                }
                if (this.objectType === 'images') {
                    this.$helpers.checkImageResolution(file);
                }
                await this.uploadFile(file);
            }
            // use queue for file with same name
            for (const file of duplicatesFiles) {
                // skip file not allowed by size and remove it from view
                if (this.$helpers.checkMaxFileSize(file) === false) {
                    this.removeProgressItem(file);
                    continue;
                }
                if (this.objectType === 'images') {
                    this.$helpers.checkImageResolution(file);
                }
                await this.uploadFile(file);
            }
        },

        onDragOver() {
            this.$el.classList.add('dragover');
        },

        onDragLeave() {
            this.$el.classList.remove('dragover');
        },

        upload(file) {
            const objectType = this.getObjectType(file);
            const formData = new FormData();
            formData.append('title', this.$helpers.titleFromFileName(file?.name || ''));
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

        uploadSuccessful(file, object) {
            object.meta.fromUpload = true;
            this.$emit('new-relations', [object]);
            this.removeProgressItem(file);
        },

        filterFiles(files) {
            const duplicates = files
                .filter((file, index, array) =>
                    array.some((val, i) =>
                        (index !== i && this.$helpers.getBaseNameFile(val) === this.$helpers.getBaseNameFile(file))
                    )
                );

            const unique = files.filter(f => !duplicates.includes(f));

            return [unique, duplicates];
        },

        getObjectType(file) {
            let type = file?.type && file?.type.split('/')[0];
            const hasPlural = /audio/g.test(type) ? '' : 's';
            if (this.knownTypes.indexOf(type) === -1) {
                type = 'file';
            }

            return `${type}${hasPlural}`;
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
</script>
<style scoped>
div.drop-upload {
    min-width: 800px;
    max-width: 1000px;
}
</style>
