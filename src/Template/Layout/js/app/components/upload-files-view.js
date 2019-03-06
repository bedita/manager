/**
 * View component used for uploading files and create media media object
 *
 *
 * @property {FileList} files
 * @property {Array} acceptedFiles
 * @property {Array} knownTypes
 *
 * @requires FetchMixin
 * @requires DragdropMixin
 */

import { PanelEvents } from 'app/components/panel-view';
import { DragdropMixin } from 'app/mixins/dragdrop';
import { FetchMixin } from 'app/mixins/fetch';
import { t } from 'ttag';

export default {
    mixins: [ DragdropMixin, FetchMixin ],

    inject: ['getCSFRToken'],

    template: /*template*/`
    <div class="upload-files">
        <section>
            <div class="upload-info"
                :key="index"
                v-for="(info, index) in Array.from(uploadProgressInfo.values())">

                <span class="name"><: info.file.name :></span>

                <button v-show="!info.error && !info.cancelled && !info.done"
                    class="abort icon-cancel"
                    :class="{'is-loading-spinner': info.pending }"
                    @click="abortUpload(info.file)">
                </button>
                <button v-show="!info.error && !info.cancelled && info.done" class="success icon-ok"></button>
                <button v-show="info.error || info.cancelled" class="retry icon-ccw" @click="tryUpload([info.file])"></button>

                <div class="progress-bar">
                    <div class="progress-bar-status" :class="progressBarCss(info)" :style="progressBarStep(info)"></div>
                </div>
                <div class="error">
                    <: info.errorMsg :>
                </div>
            </div>
        </section>
        <footer v-show="actionRequired">
            <button class="has-background-info has-text-white"
                @click.prevent="tryUpload(getFailedUploads())"><: t('Retry ') :>
            </button>
            <button class="has-background-info has-text-white"
                @click.prevent="closePanel()"><: t('Close ') :>
            </button>
        </footer>

    </div>
    `,

    props: {
        files: {
            type: FileList,
            required: true,
        },

        acceptedFiles: {
            type: Array,
            default: () => [],
        },

        knownTypes: {
            type: Array,
            default: () => ['audio', 'video', 'image'],
        }
    },

    data() {
        return {
            createdObjects: [], // list of created objects after upload
            uploadProgressInfo: new Map(), // real-time upload status
            axiosCancelTokens: new Map(), // Map of all axios cancel token
            actionRequired: false, // true when upload was manually cancelled or terminated with errors
        }
    },

    mounted() {
        // filter file list according to acceptedFiles
        // setup upload progress info list
        // start upload
        this.tryUpload(this.setupProgress(this.filterAcceptedFiles(this.files)));
    },

    destroyed() {
        this.uploadProgressInfo.clear();
    },

    methods: {
        /**
         * (View Helper) return style for progress bar according to current progress
         *
         * @param {Object} info entry for upload
         *
         * @return {Object} style object
         */
        progressBarStep(info) {
            return { width: `${info.progress}%` };
        },

        /**
         * (View Helper) return css for progress bar according to upload status
         *
         * @param {Object} info entry for upload
         *
         * @return {Object} css object
         */
        progressBarCss(info) {
            return {
                done: info.done,
                error: info.error,
                pending: info.pending,
                'in-progress': !info.done && !info.error && info.progress,
                cancelled: info.cancelled
            };
        },

        /**
         * (View Action) abort upload of file
         *
         * @param {Object} file
         *
         * @return {void}
         */
        abortUpload(file) {
            if (this.axiosCancelTokens.get(file)) {
                this.axiosCancelTokens.get(file).cancel(t('Operation canceled by the user'));
            }
        },

        /**
         * filter file list according to acceptedFiles prop
         * doesn't filter if acceptedFiles' empty
         *
         * @param {FileList} files
         *
         * @return {Array} of accettable files
         */
        filterAcceptedFiles(files = []) {
            return [...files].filter(file => {
                if (this.acceptedFiles.length === 0) {
                    return true;
                }
                return !!this.acceptedFiles.filter(accepted => file.type.includes(accepted)).length;
            } )
        },

        /**
         * setup initial upload progress list info
         *
         * @param {FileList} files
         *
         * @return {FileList} unfiltered files
         */
        setupProgress(files) {
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                this.setProgressInfo(file, 0);
            }
            return files;
        },

        /**
         * start parallel files upload
         *
         * @param {FileList} files
         *
         * @return {void}
         */
        async tryUpload(files) {
            this.actionRequired = false;

            try {
                await this.startFilesUpload(files);

                // gite the user the time to see the feedback
                setTimeout(() => PanelEvents.sendBack('upload-files:save', this.createdObjects), 500);
            } catch (err) {
                this.actionRequired = true;
            }
        },

        /**
         * set correct values for upload info entry of specified file
         * it uses a Map object, that is as of ver 2.5 of vue not reactive
         * so at the end of method this.$forceUpdate() needs to be called
         *
         * @param {File} file file
         * @param {Number} progress progress status
         * @param {Boolean} cancelled upload was cancelled
         * @param {Boolean} error upload terminated with error
         * @param {Boolean} done  upload succesful
         * @param {String} errorMsg error message
         *
         * @return {void}
         */
        setProgressInfo(file, progress = 0, cancelled = false, error = false, done = false, errorMsg = '') {
            const uploadId = `${file.lastModified}${file.size}`;
            const pending = !done && !cancelled && !error && progress === 100;

            if (progress < 0) {
                // if progress < 0 it means the caller don't know progress status but don't want to reset it in the view
                // so it recovers progress data from old uploadProgressInfo entry
                if (this.uploadProgressInfo.has(uploadId)) {
                    const info = this.uploadProgressInfo.get(uploadId);
                    progress = info.progress;
                }
            }

            this.uploadProgressInfo.set(uploadId, {
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

        /**
         * for each file start uploading process
         *
         * @param {FileList} files
         *
         * @return {Array} of axios promises
         */
        startFilesUpload(files) {
            let promises = [];
            for (let i = 0; i < files.length; i++) {
                promises.push(
                    this.createObject(files[i])
                        .then((newObject) => {
                            if (newObject) {
                                this.createdObjects.push(newObject);
                            }
                            return Promise.resolve(newObject);
                        })
                        .catch((err) => Promise.reject(err))
                );
            }
            return this.getAxios().all(promises);
        },

        /**
         * get file list of failed uploads
         *
         * @return {Array} failed files
         */
        getFailedUploads() {
            let files = [];
            this.uploadProgressInfo.forEach((element) => {
                if (element.error || element.cancelled) {
                    files.push(element.file);
                }
            });

            return files;
        },

        /**
         * create new object from file
         *
         * @param {File} file
         *
         * @return {Object|Boolean} created BEObject or false if failed creating
         */
        createObject(file) {
            if (!file) {
                return false;
            }

            // create form data
            const formData = this.createFormData(file);

            // set url
            const objectType = this.getObjectType(file);
            const url = `/${objectType}/saveJson`;

            // setup Cancel token for axios
            const CancelToken = this.getAxios().CancelToken;
            const source = CancelToken.source();
            this.axiosCancelTokens.set(file, source);

            // progress bar callback
            const handleUploadProgress = (progressEvent) => {
                const progress = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                this.setProgressInfo(file, progress);
            }

            // config object for axios request
            const config = {
                onUploadProgress: handleUploadProgress,
                onUploadCancelled: () => this.setProgressInfo(file, -1, 'Upload Cancelled'),
                onUploadError: () => this.setProgressInfo(file, 100, false, true, 'Error from server'),
                onUploadSuccess: () => this.setProgressInfo(file, 100, false, false, true),
                cancelToken: source.token
            }

            // call and returns uploadFileRequest
            return this.uploadFileRequest(url, formData, config);
        },

        /**
         * get BEobject type from file's mimetype
         *
         * @param {File} file
         *
         * @return {String} object type
         */
        getObjectType(file) {
            let type = file.type && file.type.split('/')[0];
            const hasPlural = /audio/g.test(type) ? '' : 's';

            if (this.knownTypes.indexOf(type) === -1) {
                type = 'file';
            }
            return `${type}${hasPlural}`;
        },

        /**
         * create formData object for "multipart/form-data" upload
         *
         * @param {File} file
         *
         * @return {Object} formData
         */
        createFormData(file) {
            const objectType = this.getObjectType(file);
            const formData = new FormData();

            // create formData
            formData.append('title', file.name);
            formData.append('status', 'on');
            formData.append('file', file);
            formData.append('model-type', objectType);

            return formData;
        },

        /**
         * make axios post request and hanlde error / cancellation / success accordi to callbacks object
         *
         * @param {String} url
         * @param {FormData} formData
         * @param {Objects} callbacks config file for axios
         *
         * @return {Promise} axios promise
         */
        uploadFileRequest(url, formData, callbacks) {
            return this.axios.post(url, formData, callbacks)
                .then(response => {
                    let data = response.data && response.data.data;
                    if (data) {
                        if (Array.isArray(data)) {
                            data = data[0];
                        }
                        callbacks.onUploadSuccess(data);
                        return Promise.resolve(data);
                    }
                    return Promise.reject(response);
                })
                .catch((error) => {
                    if (this.getAxios().isCancel(error)) {
                        callbacks.onUploadCancelled();
                        return Promise.reject(error);
                    }
                    callbacks.onUploadError();
                    return Promise.reject(error);
                });
        },

        /**
         * Close Panel without saving
         *
         * @returns {void}
         */
        closePanel() {
            PanelEvents.closePanel();
        },
    },
}
