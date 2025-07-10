<template>
    <article class="search-result box">
        <thumbnail :related="item" />
        <div class="box-body p-05">
            <div class="is-flex space-between align-center">
                <span :class="`tag has-background-module-${objectType}`">{{ objectType }}</span>
                <span
                    class="icon-calendar-check-o"
                    v-title="datesInfo(item)"
                />
                <span class="status is-uppercase has-text-size-smallest on">{{ item.attributes.status }}</span>
            </div>
            <div class="is-flex space-between align-center">
                <clipboard-item
                    label="ID"
                    :text="item.id"
                />
                <clipboard-item
                    label="uname"
                    :text="item.attributes.uname"
                />
            </div>
            <header class="is-flex space-between mt-05">
                <div>
                    <h3
                        class="title m-0 has-text-size-small"
                        style="padding-bottom: 7px;"
                    >
                        <span :title="item.attributes.title">{{ item.attributes.title }}</span>
                    </h3>
                </div>
            </header>
        </div>
        <footer class="is-flex space-between mt-05 p-05">
            <button
                class="is-small has-font-weight-bold mr-1"
                :class="selected ? 'button-secondary icon-check' : 'button-outlined-white button-text icon-check-empty'"
                @click.prevent="toggle()"
            >
                <template v-if="selected">
                    {{ msgDiscard }}
                </template>
                <template v-else>
                    {{ msgPick }}
                </template>
            </button>
            <object-info :object-data="item" />
            <a
                :href="`/view/${item.id}`"
                target="_blank"
                class="button button-outlined-white is-small"
            >
                <app-icon icon="carbon:launch" />
                <span class="ml-05">{{ msgOpen }}</span>
            </a>
        </footer>
    </article>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'SearchResult',
    props: {
        item: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            msgDiscard: t`Discard`,
            msgOpen: t`Open`,
            msgPick: t`Pick`,
            objectType: this.item?.type || 'unknown',
            selected: false,
        };
    },
    methods: {
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
        toggle() {
            this.selected = !this.selected;
            const event = this.selected ? 'select' : 'discard';
            this.$emit(event, this.item.id);
        },
    },
};
</script>
<style scoped>
article.search-result {
    flex: 1;
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 12px;
    background-color: #fff;
}
article.search-result .thumbnail {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 63%;
    background-color: #f8f9fa;
    pointer-events: none;
}
article.search-result .thumbnail figure {
    font-size: 2.5rem;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    margin: 0 !important;
}
article.search-result .thumbnail figure img {
    display: block;
    object-fit: cover;
    width: 100%;
    height: 100%;
}
</style>
