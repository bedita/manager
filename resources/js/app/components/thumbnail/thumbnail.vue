<template>
    <div class="thumbnail" :class="'has-text-module-' + related?.type">
        <figure class="center-icon icon-opaque" :class="figureClass()">
            <img :src="related?.meta?.thumb_url" v-if="showImage()" />
            <Icon icon="carbon:cube" v-if="showIcon()"></Icon>
        </figure>
    </div>
</template>
<script>

export default {

    props: {
        related: {
            type: Object,
            default: {}
        },
    },

    methods: {
        figureClass() {
            return this.showImage() ? '' : `icon-${this.related?.type}`;
        },
        showIcon() {
            const iconTypes = ['documents', 'events', 'files', 'folders', 'news', 'profiles', 'users'];

            return !this.showImage() && !iconTypes.includes(this.related?.type);
        },
        showImage() {
            return this.related?.meta && this.related?.meta?.thumb_url;
        },
    },
}
</script>
