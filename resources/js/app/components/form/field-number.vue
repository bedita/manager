<template>
    <input
        :id="id"
        :name="name"
        :data-ref="reference"
        :value="value"
        class="field-number"
        step="any"
        type="number"
        @change="change($event.target.value)"
    >
</template>
<script>
export default {
    name: 'FieldNumber',
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
<style scoped>
.field-number {
    width: 200px;
}
</style>
