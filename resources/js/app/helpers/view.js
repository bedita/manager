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

            /**
             * Get file name without extension
             * @param {Object} file file
             * @returns {String} basename file
             */
            getBaseNameFile(file) {
                return file?.name?.split('.')[0];
            },

            slugify(str, len) {
                if (!str) {
                    return str;
                }

                let slug = str.trim().toLowerCase().replace(/[^0-9a-z]/gi, '-');
                slug = this.removeDuplicates(slug, '-');


                return slug.substring(0, len);
            },

            removeDuplicates(text, char) {
                let curr = '';
                let prev = '';
                let ret = text;
                for (let i = 0; i < text.length; i++) {
                    curr = ret.charAt(i);
                    if (curr === char && curr === prev) {
                        if (i > ret.length) {
                            return ret;
                        }
                        ret = ret.slice(0, i) + ret.slice(i+1)
                        i--;
                    } else {
                        prev = curr;
                    }
                }

                return ret;
            },

            acceptMimeTypes(type) {
                if (!BEDITA.uploadConfig?.accepted?.[type]) {
                    return '';
                }

                return BEDITA.uploadConfig?.accepted?.[type].join(',');
            },

            checkMimeForUpload(file, objectType) {
                const fileType = file?.type || '';

                /** forbidden mime types check */
                const forbidden = BEDITA.uploadConfig?.forbidden?.mimetypes || [];
                if (forbidden.includes(fileType)) {
                    const msg = t`File type forbidden` + `: "${fileType}". ` + t`Forbidden types` + `: "${forbidden.join('", "')}".`;
                    BEDITA.warning(msg);

                    return false;
                }

                /** forbidden extensions check */
                const extensions = BEDITA.uploadConfig?.forbidden?.extensions || [];
                const fileExtension = file.name.split('.').pop();
                if (extensions.includes(fileExtension)) {
                    const msg = t`File extension forbidden` + `: "${fileExtension}". ` + t`Forbidden extensions` + `: "${extensions.join('", "')}".`;
                    BEDITA.warning(msg);

                    return false;
                }

                /** accepted mime types check */
                const mimes = BEDITA.uploadConfig?.accepted;
                if (mimes?.[objectType] && !this.checkAcceptedMime(mimes[objectType], fileType)) {
                    const msg = t`File type not accepted` + `: "${fileType}". ` + t`Accepted types` + `: "${mimes[objectType].join('", "')}".`;
                    BEDITA.warning(msg);

                    return false;
                }

                return true;
            },

            checkAcceptedMime(whitelist, mime) {
                if (whitelist.includes(mime)) {
                    return true;
                }
                const type = mime.substring(0, mime.lastIndexOf('/'));
                for (let acceptedMime of whitelist || []) {
                    if (acceptedMime.endsWith('/*')) {
                        const acceptedType = acceptedMime.substring(0, acceptedMime.lastIndexOf('/'));
                        if (type === acceptedType) {
                            return true;
                        }
                    }
                }
                return false;
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

            minLength(len) {
                return t`At least ${len} characters`;
            },
        }
    }
};
