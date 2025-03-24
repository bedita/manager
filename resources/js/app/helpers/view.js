import { t } from 'ttag';
import { warning } from 'app/components/dialog/dialog';
import { humanizeString } from 'app/helpers/text-helper.js';

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
                const maxFileSize = BEDITA.maxFileSize;
                if (maxFileSize < 0) {// no limit
                    return true;
                }
                const fileSize = Math.round(file.size);
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
             * Check image resolution.
             * Open a warning dialog, if too big.
             * @param {Object} file The image file to check
             * @returns {void}
             */
            checkImageResolution(file) {
                const resolution = BEDITA.uploadConfig.maxResolution;
                const parts = resolution.split('x');
                const maxX = parts[0];
                const maxY = parts[1];
                const img = new Image();
                img.src = window.URL.createObjectURL(file);
                img.onerror = (e) => {
                    window.URL.revokeObjectURL(img.src);
                    console.error('error', e);
                    const filename = file.name;
                    const message = t`We could not calculate the resolution of the file ${filename}. Please select a file with a resolution less than or equal to ${resolution}`;
                    warning(message);
                };
                img.onload = () => {
                    const resolutionOk = (img.width <= maxX && img.height <= maxY) || (img.width <= maxY && img.height <= maxX);
                    window.URL.revokeObjectURL(img.src);
                    if (resolutionOk) {
                        return;
                    }
                    const filename = file.name;
                    const message = t`Resolution of ${filename} is too big (${img.width}x${img.height}), please select a file whose resolution is less than or equal to ${resolution}`;
                    warning(message);
                };
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
                if (['files', 'media'].includes(type) || !BEDITA.uploadConfig?.accepted?.[type]) {
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
                const ot = objectType === 'media' ? this.getObjectTypeFromMime(fileType) : objectType;
                if (ot !== 'files' && mimes?.[ot] && !this.checkAcceptedMime(mimes[ot], fileType)) {
                    const msg = t`File type not accepted` + `: "${fileType}". ` + t`Accepted types` + `: "${mimes[ot].join('", "')}".`;
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

            getObjectTypeFromMime(mimeType) {
                const mimes = BEDITA.uploadConfig?.accepted;
                for (let type in mimes) {
                    if (this.checkAcceptedMime(mimes[type], mimeType)) {
                        return type;
                    }
                }

                return null;
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
                if (typeof str !== 'string') {
                    return str;
                }
                if (str.length <= len) {
                    return str;
                }
                const ellipsis = '[...]';

                return str.substring(0, len - ellipsis.length) + ellipsis;
            },

            minLength(len) {
                return t`At least ${len} characters`;
            },

            debounce(fn, timeout = 500) {
                let timer = null;

                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        fn.apply(this, args);
                    }, timeout);
                };
            },

            getLastWeeksDate() {
                const now = new Date();

                return new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
            },

            humanize(str) {
                return humanizeString(str);
            },

            stripHtml(str) {
                return str.replace(/<\/?[^>]+(>|$)/g, '');
            },

            convertFromPoint(input) {
                if (!input) {
                    return;
                }
                let match = input.match(/point\(([^)]*)\)/i);
                if (!match) {
                    return;
                }
                return match[1].split(' ').join(', ');
            },

            convertToPoint(input) {
                if (!input) {
                    return;
                }
                if (input.match(/point\(([^)]*)\)/i)) {
                    return input;
                }

                let [lon, lat] = input.split(/\s*,\s*/);
                return `POINT(${lon} ${lat})`;
            },

            formatDate(d) {
                const locale = BEDITA?.locale?.slice(0, 2) || 'en';

                return d ?  new Date(d).toLocaleDateString(locale) + ' ' + new Date(d).toLocaleTimeString(locale) : '';
            },

            formatBytes(size) {
                let i = size == 0 ? 0 : Math.floor( Math.log(size) / Math.log(1024) );

                return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
            },
        }
    }
};
