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

import { Icon } from '@iconify/vue2';

export default {

    components: {
        Icon,
    },

    data() {
        return {
            file: null,
        }
    },

    methods: {
        fileAcceptMimeTypes(type) {
            return this.$helpers.acceptMimeTypes(type);
        },

        onFileChange(e, type) {
            this.file = null;
            if (this.$helpers.checkMimeForUpload(e.target.files[0], type) === false) {
                return;
            }
            if (this.$helpers.checkMaxFileSize(e.target.files[0]) === false) {
                return;
            }
            this.file = e.target.files[0];
            this.$helpers.setTitleFromFileName('title', this.file.name);
        },

        resetFile(e) {
            e.target.parentNode.querySelector('input.file-input').value = '';
            this.file = null;
        },

        previewImage() {
            return this.$helpers.updatePreviewImage(this.file, 'title');
        },
    }
}
