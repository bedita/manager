<template>
    <input
        :id="id"
        :data-ref="reference"
        class="field-checkbox"
        type="checkbox"
        :checked="!!v"
        @change="change($event.target.checked)"
    >
</template>
<script>
export default {
    name: 'FieldCheckbox',
    props: {
        id: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        reference: {
            type: String,
            default: ''
        },
        value: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            v: this.value,
        };
    },
    methods: {
        change(val) {
            this.$emit('change', val);
        }
    },
}
</script>
