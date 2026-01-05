<template>
    <input
        :id="id"
        :name="name"
        :data-ref="reference"
        :value="value"
        class="field-integer"
        type="number"
        @change="change($event.target.value)"
    >
</template>
<script>
export default {
    name: 'FieldInteger',
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
        }
    },
}
</script>
<style scoped>
.field-integer {
    width: 200px;
}
</style>
