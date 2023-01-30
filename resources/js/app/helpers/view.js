import { t } from 'ttag';
import { warning } from 'app/components/dialog/dialog';

export default {
    install (Vue) {
        Vue.prototype.$helpers = {

            /**
            * Build view url using object ID
            *
            * @param {Number} objectId
            *
            * @return {String} url
            */
            buildViewUrl(objectId) {
                return `${BEDITA.base}/view/${objectId}`;
            },

            /**
            * Build view url usiong object type and ID
            *
            * @param {String} objectType
            * @param {Number} objectId
            *
            * @return {String} url
            */
            buildViewUrlType(objectType, objectId) {
                return `${BEDITA.base}/${objectType}/view/${objectId}`;
            },

            /**
            * Force download using a syntetic element
            *
            * @param {*} blob
            * @param {*} filename
            */
            forceDownload(blob, filename) {
                let a = document.createElement('a');
                a.download = filename;
                a.href = blob;
                a.click();
            },

            /**
            * download a resource as a blob to avoid cors restrictions
            *
            * @param {string} url
            * @param {string} filename
            */
            downloadResource(url, filename) {
                if (!filename) {
                    filename = url.split('\\').pop().split('/').pop();
                }

                const options = {
                    headers: new Headers({
                        'Origin': location.origin
                    }),
                    mode: 'cors'
                }

                fetch(url, options)
                    .then(response => response.blob())
                    .then(blob => {
                        let blobUrl = window.URL.createObjectURL(blob);
                        this.forceDownload(blobUrl, filename);
                    })
                    .catch(e => console.error(e));
            },

            /**
             * Check if file is bigger than max file size.
             * Open a warning dialog and return false, if too big.
             * Return true otherwise.
             *
             * @param {Object} file The file to check
             * @returns {Boolean}
             */
            checkMaxFileSize(file) {
                const fileSize = Math.round(file.size);
                const maxFileSize = BEDITA.maxFileSize;
                if (fileSize >= maxFileSize) {
                    const filename = file.name;
                    const fileSizeMb = Math.round(fileSize / 1024 / 1024);
                    const maxFileSizeMb = Math.round(BEDITA.maxFileSize / 1024 / 1024);
                    const message = t`File "${filename}" too big (${fileSizeMb} MB), please select a file less than max file size (${maxFileSizeMb} MB)`;
                    warning(message);

                    return false;
                }

                return true;
            },

            slugify(str) {
                if (!str) {
                    return str;
                }

                return str.toLowerCase().replaceAll(' ', '-');
            },

            acceptMimeTypes(type) {
                if (!BEDITA.uploadMimeTypes?.[type]) {
                    return '';
                }

                return BEDITA.uploadMimeTypes?.[type].join(',');
            },

            checkMimeForUpload(file, objectType) {
                /** accepted mime types by object type for file upload */
                const mimes = BEDITA.uploadMimeTypes;
                if (mimes?.[objectType] && !mimes[objectType].includes(file.type)) {
                    const msg = t`File type not accepted` + `: "${file.type}". ` + t`Accepted types` + `: "${mimes[objectType].join('", "')}".`;
                    BEDITA.warning(msg);

                    return false;
                }

                return true;
            },

            titleFromFileName(filename) {
                let title = filename;
                title = title.replaceAll('-', ' ');
                title = title.replaceAll('_', ' ');
                if (title.lastIndexOf('.') > 0) {
                    title = title.substring(0, title.lastIndexOf('.')) || title;
                }

                return title.trim();
            },

            setTitleFromFileName(titleId, fileName) {
                const elem = document.getElementById(titleId);
                if (!elem || elem.value !== '') {
                    return;
                }
                elem.value = this.titleFromFileName(fileName);
            },

            updatePreviewImage(file, titleId, thumb) {
                if (file?.name) {
                    if (titleId) {
                        this.setTitleFromFileName(titleId, file.name);
                    }

                    return window.URL.createObjectURL(file);
                }

                return thumb || null;
            },

            truncate(str, len) {
                if (str.length <= len) {
                    return str;
                }
                const ellipsis = '[...]';

                return str.substring(0, len - ellipsis.length) + ellipsis;
            },
        }
    }
};
