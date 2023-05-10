<template>
</template>
<script>
import { JSONEditor } from 'vanilla-jsoneditor';

export default {
    name: 'json-editor',

    components: {
        JSONEditor,
    },

    props: {
        target: {
            type: String,
            default: '',
        },
        options: {
            type: Object,
            default: () => ({}),
        },
        text: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            editor: null,
        };
    },

    mounted() {
        const element = document.getElementById(this.target);
        const options = Object.assign({
            content: { text: this.text },
            readOnly: true
        }, this.options);
        this.editor = new JSONEditor({
            target: element,
            props: options
        });
    },
}
</script>
