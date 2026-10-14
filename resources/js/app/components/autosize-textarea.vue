<template>
    <textarea
        :value="text"
        @input="handleChange"
    />
</template>
<script>
import autosize from 'autosize';

export default {
    name: 'AutosizeTextarea',
    props: {
        value: {
            type: String,
            default: '',
        },
        resetValue: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            text: {
                type: String,
                default: '',
            },
            originalValue: {
                type: String,
                default: '',
            },
        };
    },

    watch: {
        text() {
            // make sure render reactively
            this.$nextTick(() => {
                autosize(this.$el);
            });
        },

        value() {
            this.originalValue = this.value;
        },
    },

    mounted() {
        // setup initial value
        this.originalValue = this.value;
        this.text = this.value;

        // setup autosize
        this.$nextTick(() => {
            autosize(this.$el);
        });
    },

    methods: {
        handleChange(event) {
            this.text = event.target.value;
            this.$emit('input', this.text);
        }
    }
};
</script>
