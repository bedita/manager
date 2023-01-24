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
        }
    },

    methods: {
        onFileChanged(e, type) {
            this.file = null;
            if (this.$helpers.checkMimeForUpload(e.target.files[0], type) === false) {
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
                if (document.getElementById('title') && document.getElementById('title').value === '') {
                    document.getElementById('title').value = this.titleFromFileName(this.file.name);
                }
                return window.URL.createObjectURL(this.file);
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
    }
}
