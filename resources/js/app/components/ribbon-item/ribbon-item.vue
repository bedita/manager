<template>
    <div
        class="ribbon-item ribbon"
        :class="itemClass(item, dataList)"
        v-if="visible"
    >
        <span v-if="itemLabel(item)">{{ itemLabel(item) }}</span>
    </div>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';

export default {
    name: 'RibbonItem',
    props: {
        dataList: {
            type: Boolean,
            default: false
        },
        item: {
            type: Object,
            required: true
        },
        stage: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            msgExpired: t`Expired`,
            msgFuture: t`Future`,
            msgNew: t`New`,
            msgUploaded: t`Uploaded`,
        };
    },
    computed: {
        visible() {
            return this.stage || Date.parse(this.item.attributes.publish_start) > Date.now() || Date.parse(this.item.attributes.publish_end) < Date.now();
        },
    },
    methods: {
        itemClass(item, dataList) {
            const type = item?.type;

            return `ribbon ${type ? 'has-background-module-' + type : ''} ${dataList ? 'in-data-list' : ''}`;
        },

        itemLabel(item) {
            if (this.stage) {
                return this.itemNew(item) && item?.meta?.fromUpload ? this.msgUploaded : this.msgNew;
            }
            if (!item.attributes.publish_start) {
                return '';
            }

            return Date.parse(item.attributes.publish_start) > Date.now() ? this.msgFuture : this.msgExpired;
        },

        itemNew(item) {
            const now = new Date();
            const sd = moment(item.meta.created);
            const ed = moment(now);
            const diff = moment.duration(ed.diff(sd)).asMinutes();

            return diff <= 1;
        },
    },
}
</script>
<style scoped>
div.ribbon-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
div.ribbon-item span {
    margin-right: 0.5rem;
}
</style>
