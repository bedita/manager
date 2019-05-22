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
            this.fileName = e.target.files[0].name;
        },

        resetFile(e) {
            e.target.parentNode.querySelector('input.file-input').value = '';
            this.fileName = '';
        }
    }
}
