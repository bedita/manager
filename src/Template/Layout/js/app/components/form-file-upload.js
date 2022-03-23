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
 *
 */

import { warning } from 'app/components/dialog/dialog';
import { t } from 'ttag';

export default {
    props: {
    },

    data() {
        return {
            fileName: '',
        }
    },

    mounted() {
    },

    methods: {
        onFileChanged(e) {
            const fileSize = Math.round(e.target.files[0].size);
            const maxFileSize = BEDITA.maxFileSize;
            if (fileSize >= maxFileSize) {
                this.fileName = '';
                const fileSizeMb = Math.round(fileSize / 1024 / 1024);
                const maxFileSizeMb = Math.round(BEDITA.maxFileSize / 1024 / 1024);
                const message = t`File too big (${fileSizeMb} MB), please select a file less than max file size (${maxFileSizeMb} MB)`;
                warning(message);

                return;
            }
            this.fileName = e.target.files[0].name;
        },

        resetFile(e) {
            e.target.parentNode.querySelector('input.file-input').value = '';
            this.fileName = '';
        }
    }
}
