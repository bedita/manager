<template>
    <label
        class="field-title"
        :for="field"
    >
        {{ title }}
        <span v-if="required">*</span>
    </label>
</template>
<script>
export default {
    name: 'FieldTitle',
    props: {
        field: {
            type: String,
            required: true
        },
        required: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            required: true
        },
    }
}
</script>
<style scoped>
label.field-title > span {
    color: red;
}
</style>
