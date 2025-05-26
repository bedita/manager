<template>
    <div class="tree-content">
        <span><app-icon icon="carbon:document" /></span>
        <span class="tag is-smallest mx-05" :class="`has-background-module-${obj?.type}`">{{ obj?.type }}</span>
        <span v-title="obj?.attributes?.title">{{ truncate(obj?.attributes?.title, 80) }}</span>
        <span class="modified">{{ $helpers.formatDate(obj?.meta?.modified) }}</span>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'TreeContent',
    props: {
        obj: {
            type: Object,
            required: true
        },
    },
    methods: {
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
    },
}
</script>
<style scoped>
div.tree-content {
    padding-left: 0.5rem;
}
</style>
