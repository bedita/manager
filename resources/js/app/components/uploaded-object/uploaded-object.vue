<template>
    <article
        class="uploaded-object"
        :class="`${item.type} has-shadow-color-${item.type} has-status-${item.attributes.status}`"
    >
        <div class="left">
            <span
                class="tag"
                :class="'has-background-module-' + item.type"
            >
                {{ item.type }}
            </span>
        </div>
        <div class="left">
            <span class="icon-calendar-check-o" v-title="datesInfo(item)" />
            <span
                class="status is-uppercase has-text-size-smallest"
                :class="item.attributes.status"
                v-if="item?.attributes?.status"
            >
                {{ item.attributes.status }}
            </span>
        </div>
        <div>
            <clipboard-item
                label="ID"
                :text="item.id.toString()"
            />
        </div>
        <div>
            <clipboard-item
                label="uname"
                :text="item.attributes.uname.toString()"
            />
        </div>
        <div class="left">
            <span
                :title="item?.attributes?.title || item?.attributes?.name || item?.attributes?.uname || '-'"
                v-html="$helpers.truncate(item?.attributes?.title || item?.attributes?.name || item?.attributes?.uname || '-', 50)"
            />
        </div>
        <div>
            <a
                class="button button-outlined is-small mr-1"
                :href="$helpers.buildViewUrl(item.id)"
                target="_blank"
            >
                <app-icon icon="carbon:launch" />
                <span class="ml-05">{{ msgEdit }}</span>
            </a>
        </div>
        <div>
            <div
                :class="`ribbon ${item?.type ? 'has-background-module-' + item?.type : ''}`"
                v-if="createdNow(item)"
            >
                {{ msgNew }}
            </div>
        </div>
    </article>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';

export default {
    name: 'UploadedObject',
    components: {
        ClipboardItem: () => import(/* webpackChunkName: "clipboard-item" */'app/components/clipboard-item/clipboard-item'),
        Thumbnail:() => import(/* webpackChunkName: "thumbnail" */'app/components/thumbnail/thumbnail'),
    },
    props: {
        item: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            msgEdit: t`Edit`,
            msgNew: t`NEW`,
        }
    },
    methods: {
        createdNow(item) {
            const creationDate = item?.meta?.created;
            if (!creationDate) {
                return false;
            }
            const start = moment(new Date());
            const end = moment(new Date(creationDate));
            const diffDays = Math.abs(Math.round(moment.duration(end.diff(start)).asDays()));
            const diffHours = Math.abs(Math.round(moment.duration(end.diff(start)).asHours()));
            const diffMinutes = Math.abs(Math.round(moment.duration(end.diff(start)).asMinutes()));

            return diffDays === 0 && diffHours === 0 && diffMinutes === 0;
        },

        datesInfo(obj) {
            if (obj?.meta?.created === undefined || obj?.meta?.modified === undefined) {
                return '';
            }
            const created = new Date(obj.meta.created).toLocaleDateString() + ' ' + new Date(obj.meta.created).toLocaleTimeString();
            const modified = new Date(obj.meta.modified).toLocaleDateString() + ' ' + new Date(obj.meta.modified).toLocaleTimeString();
            if (!obj?.attributes?.publish_start) {
                return t`Created on ${created}.` + ' ' + t`Modified on ${modified}.`;
            }
            const published = new Date(obj.meta.publish_start).toLocaleDateString() + ' ' + new Date(obj.attributes.publish_start).toLocaleTimeString();

            return t`Created on ${created}.` + ' ' + t`Modified on ${modified}.` + ' ' + t`Publish start on ${published}.`;
        },
    },
}
</script>
<style scoped>
article.uploaded-object {
    border-bottom: 1px solid #8e959e;
    margin-bottom: 1rem;
    overflow: hidden;
    display: grid;
    grid-template-columns: 10% 10% 10% 10% 40% 10% 10%;
    min-width: 800px;
    max-width: 1000px;
}
article.uploaded-object > div {
    display: flex;
    padding: 0.5rem;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}
article.uploaded-object > div.left {
    justify-content: flex-start;
}
article.uploaded-object .ribbon {
    padding: 0.25rem;
    color: #fff;
    font-weight: bold;
    font-size: 0.75rem;
    border-radius: 0.5rem;
}
</style>
