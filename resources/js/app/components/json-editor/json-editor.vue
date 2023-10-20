<template>
    <div class="json-editor" />
</template>
<script>
import { JSONEditor } from 'vanilla-jsoneditor';

export default {
    name: 'JsonEditor',

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
        const options = {content: { text: this.text }, readOnly: true, ...this.options};
        this.editor = new JSONEditor({
            target: element,
            props: options
        });
    },
}
</script>
