<template>
    <input
        :id="id"
        :name="name"
        :data-ref="reference"
        :value="value"
        class="field-string"
        type="text"
        @change="change($event.target.value)"
    >
</template>
<script>
export default {
    name: 'FieldString',
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
    methods: {
        change(value) {
            this.$emit('change', value);
        },
    },
}
</script>
