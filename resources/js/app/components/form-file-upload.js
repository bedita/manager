/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <form-file-upload>
 *
 * form form-file-upload element
 *
 * @prop {Array} labels
 * @prop {int} defaultActive index of the default active label in labels
 */

import { t } from 'ttag';

export default {

    data() {
        return {
            file: null,
            /** accepted mime types by object type for file upload */
            mimes: {
                images: [
                    'image/apng',
                    'image/bmp',
                    'image/jp2',
                    'image/jpeg',
                    'image/jpg',
                    'image/gif',
                    'image/png',
                    'image/svg+xml',
                    'image/webp',
                ],
            },
        }
    },

    methods: {
        onFileChanged(e, type) {
            this.file = null;
            if (this.mimes?.[type] && !this.mimes[type].includes(e.target.files[0].type)) {
                const msg = t`File type not accepted` + `: "${e.target.files[0].type}". ` + t`Accepted types` + `: "${this.mimes[type].join('", "')}".`;
                BEDITA.warning(msg);

                return;
            }
            if (this.$helpers.checkMaxFileSize(e.target.files[0]) === false) {
                return;
            }
            this.file = e.target.files[0];
        },

        resetFile(e) {
            e.target.parentNode.querySelector('input.file-input').value = '';
            this.file = null;
        },

        previewImage() {
            if (this.file?.name) {
                return window.URL.createObjectURL(this.file);
            }

            return null;
        },
    }
}
