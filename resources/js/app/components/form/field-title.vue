<template>
    <label
        class="field-title"
        :for="field"
    >
        {{ getTitle() }}
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
    },
    methods: {
        getTitle() {
            return BEDITA_I18N?.[this.field] || this.title;
        },
    },
}
</script>
<style scoped>
label.field-title > span {
    color: red;
}
</style>
