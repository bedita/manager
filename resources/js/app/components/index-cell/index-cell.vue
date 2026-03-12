<template>
    <div
        :class="className()"
        untitled-label="${t`Untitled`}"
        @mouseover="onMouseover()"
        @mouseleave="onMouseleave()"
    >
        <template v-if="!msg && truncated">
            <div
                v-html="truncated"
                v-if="renderAs === 'string'"
            />
            <div
                class="json-code"
                v-html="JSON.stringify(truncated ? JSON.parse(truncated) : null, null, 2)"
                v-if="renderAs === 'object'"
            />
        </template>
        <div
            class="ml-05"
            @click.stop.prevent="copy()"
            v-if="showCopyIcon()"
        >
            <app-icon icon="carbon:copy" />
        </div>
        <div
            class="msg"
            v-if="msg"
        >
            <app-icon
                icon="carbon:checkmark"
                color="green"
            />
            <span>{{ msg }}</span>
        </div>
        <template v-if="related?.length > 0">
            <div class="related-container">
                <div class="related-item">
                    <span
                        class="ml-05"
                        v-for="f in relatedFields"
                        :key="f"
                        @click.stop.prevent="openRelated(related?.[0])"
                    >
                        <template v-if="f == 'media_url'">
                            <img :src="thumb(0)">
                        </template>
                        <template v-else>
                            {{ related?.[0]?.attributes?.[f] || related?.[0]?.meta?.[f] }}
                        </template>
                    </span>
                    <span
                        class="open-new-tab-icon"
                        v-if="hasRelatedLink(related?.[0])"
                    >
                        <app-icon icon="carbon:launch" />
                    </span>
                </div>
                <div
                    class="related-toggle"
                    v-if="related?.length > 1"
                >
                    <button
                        class="show-toggle icon icon-only-icon"
                        :title="relatedToggleLabel()"
                        @click.stop.prevent="relatedOpen = !relatedOpen"
                        v-if="relatedOpen"
                    >
                        <app-icon icon="carbon:subtract" />
                        <span class="is-sr-only">{{ relatedToggleLabel() }}</span>
                    </button>
                    <button
                        class="show-toggle icon icon-only-icon"
                        :title="relatedToggleLabel()"
                        @click.stop.prevent="relatedOpen = !relatedOpen"
                        v-else
                    >
                        <app-icon icon="carbon:add" />
                        <span class="is-sr-only">{{ relatedToggleLabel() }}</span>
                    </button>
                    <span class="tag is-smallest is-black mx-05 related-count empty">
                        {{ relatedCountLabel() }}
                    </span>
                </div>
            </div>
            <div
                class="related-list"
                v-if="relatedOpen"
            >
                <template v-for="(relatedItem, index) in related">
                    <div
                        class="related-item"
                        @click.stop.prevent="relatedOpen = !relatedOpen"
                        :key="index"
                        v-if="index > 0"
                    >
                        <span
                            class="ml-05"
                            v-for="f in relatedFields"
                            :key="f"
                            @click.stop.prevent="openRelated(relatedItem)"
                        >
                            <template v-if="f == 'media_url'">
                                <img :src="thumb(index)">
                            </template>
                            <template v-else>
                                {{ relatedItem?.attributes?.[f] || relatedItem?.meta?.[f] }}
                            </template>
                        </span>
                        <span
                            class="open-new-tab-icon"
                            v-if="hasRelatedLink(relatedItem)"
                        >
                            <app-icon icon="carbon:launch" />
                        </span>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>
<script>
import { t } from 'ttag';
export default {
    name: 'IndexCell',
    props: {
        prop: {
            type: String,
            default: '',
        },
        related: {
            type: Array,
            default: () => [],
        },
        relatedFields: {
            type: Array,
            default: () => [],
        },
        relatedThumbs: {
            type: Array,
            default: () => [],
        },
        schema: {
            type: [Object, Array],
            default: () => ({}),
        },
        settings: {
            type: Object,
            default: () => ({}),
        },
        text: {
            type: String,
            default: '',
        },
        untitledlabel: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            msg: '',
            msgMore: t`More`,
            renderAs: 'string',
            relatedOpen: false,
            showCopy: false,
            truncated: '',
        };
    },
    async mounted() {
        this.$nextTick(() => {
            if (Object.keys(this.schema)?.length) {
                const oneOf = this.schema?.['oneOf'] || null;
                let isObject = false;
                if (oneOf && oneOf?.length > 1) {
                    const index = oneOf?.length > 1 ? 1 : 0;
                    isObject = JSON.stringify(oneOf[index]) === '{}' || JSON.stringify(oneOf[index]) === 'true' || oneOf[index]?.['type'] === 'object';
                } else {
                    isObject = JSON.stringify(this.schema) === '{}' || JSON.stringify(this.schema) === 'true' || this.schema?.['type'] === 'object';
                }
                this.renderAs = isObject ? 'object' : 'string';
            }
            let text = this.text || '';
            const isHtml = this.contentMediaTypeHtml();
            if (this.schema && this.schema?.['$id'] != '/properties/title' && isHtml && this.renderAs === 'string') {
                // strip HTML tags if contentMediaType is text/html
                const div = document.createElement('div');
                div.innerHTML = text;
                text = div.textContent || div.innerText || '';
            }
            this.truncated = (text?.length <= 100 || this.renderAs === 'object') ? text : text?.substring(0, 100);
        });
    },
    methods: {
        className() {
            return this.related?.length ? 'index-cell related-cell' : `index-cell ${this.prop}-cell`;
        },
        contentMediaTypeHtml() {
            if (!this.schema) {
                return false;
            }
            const oneOf = this.schema?.['oneOf'] || null;
            if (!oneOf) {
                return false;
            }
            const contentMediaType = oneOf.find((o) => o?.type === 'string' && o?.contentMediaType === 'text/html');

            return !!contentMediaType;
        },
        copy() {
            navigator.clipboard.writeText(this.text.replace(/<[^>]*>/g, ''));
            this.msg = t`copied in the clipboard`;
            setTimeout(() => this.reset(), 2000)
        },
        onMouseover() {
            if (this.settings?.copy2clipboard !== true) {
                return;
            }
            this.showCopy = true;
        },
        onMouseleave() {
            if (this.settings?.copy2clipboard !== true) {
                return;
            }
            this.showCopy = false;
        },
        reset() {
            this.msg = '';
        },
        hasRelatedLink(relatedItem) {
            if (relatedItem?.type === 'roles') {
                return false;
            }
            return !!relatedItem?.id;
        },
        relatedCountLabel() {
            const count = this.related?.length || 0;
            if (!count) {
                return '';
            }

            return `${this.relatedOpen ? count : 1}/${count}`;
        },
        relatedToggleLabel() {
            return this.relatedCountLabel() || this.msgMore;
        },
        openRelated(relatedItem) {
            const id = relatedItem?.id || null;
            if (!id) {
                return;
            }
            const url = this.$helpers?.buildViewUrl ? this.$helpers.buildViewUrl(id) : `/view/${id}`;
            const newTab = window.open(url, '_blank');
            if (newTab) {
                newTab.focus();
            }
        },
        showCopyIcon() {
            if (this.settings?.copy2clipboard !== true) {
                return false;
            }
            return !this.msg && this.showCopy;
        },
        thumb(index) {
            const thumb = this.relatedThumbs?.[index] || false;
            const thumbError = !isNaN(thumb) && Number(thumb) < 0;

            return !thumbError ? thumb : this.related?.[index]?.meta?.media_url;
        },
    },
};
</script>
<style scoped>
div.index-cell > div {
    display: inline-block;
    line-clamp: 1;
}
div.index-cell > div.msg {
    color: forestgreen;
    font-family: monospace;
    font-style: italic;
}
div.index-cell div.related-container {
    display: flex;
    align-items: flex-start;
}
div.index-cell div.related-item {
    display: flex;
    flex-wrap: wrap;
    min-width: 0;
    color: #e6edf3;
    align-items: center;
    border-radius: 0.2rem;
    transition: background-color 0.12s ease-in-out;
}

div.index-cell div.related-item > span {
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    cursor: pointer;
}
div.index-cell div.related-list {
    margin-top: 0.35rem;
    margin-left: 0;
}
div.index-cell div.related-list div.related-item {
    gap: 0.15rem 0.45rem;
    padding: 0.2rem 0;
}
div.index-cell div.related-list div.related-item + div.related-item {
    margin-top: 0.2rem;
}
div.index-cell div.related-item:hover {
    background-color: #2f3943;
}
div.index-cell div.related-item .open-new-tab-icon {
    opacity: 0;
    margin-left: 0.3rem;
    color: #c5d0db;
    transition: opacity 0.12s ease-in-out;
    pointer-events: none;
}
div.index-cell div.related-item:hover .open-new-tab-icon {
    opacity: 1;
}
div.index-cell div.related-toggle {
    display: flex;
}
div.index-cell div.related-toggle > button {
    margin-left: 16px;
    line-height: 1;
    height: 20px;
    cursor: cell;
}
div.index-cell span.related-count {
    user-select: none;
}
div.index-cell img {
    margin-bottom: 0.2rem;
    padding: 1px;
    display: inline-block;
    max-height: 32px;
    max-width: 40px;
    vertical-align: middle;
    background-color: #e9ecef;
}
div.index-cell div.json-code {
    white-space: pre;
    font-family: monospace;
    font-size: x-small;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 200px;
    max-width: 400px;
}
</style>
