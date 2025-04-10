<template>
    <div class="json-editor" />
</template>
<script>
import { JSONEditor } from 'vanilla-jsoneditor';

export default {
    name: 'JsonEditor',

    props: {
        name: {
            type: String,
            default: '',
        },
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
        const options = {
            content: { text: this.text },
            readOnly: true,
            ...this.options
        };
        if (!options?.onChange) {
            options.onChange = this.handleChange;
        }
        this.editor = new JSONEditor({
            target: element,
            props: options
        });
        element.jsonEditor = this.editor;
    },

    methods: {
        handleChange(updatedContent, previousContent, { contentErrors, patchResult }) {
            this.$emit('change', this.name, updatedContent, previousContent, contentErrors, patchResult);
        }
    },
}
</script>
