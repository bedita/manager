<template>
    <div
        :class="className()"
        untitled-label="${t`Untitled`}"
        @mouseover="onMouseover()"
        @mouseleave="onMouseleave()"
    >
        <div
            v-html="truncated"
            v-if="!msg && truncated && renderAs === 'string'"
        />
        <div
            class="json-code"
            v-if="!msg && renderAs === 'object'"
        >{{ JSON.stringify(text ? JSON.parse(text) : null, null, 2) }}</div>

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
                    >
                        <template v-if="f == 'media_url'">
                            <img :src="thumb(0)">
                        </template>
                        <template v-else>
                            {{ related?.[0]?.attributes?.[f] || related?.[0]?.meta?.[f] }}
                        </template>
                    </span>
                </div>
                <div
                    class="related-toggle"
                    v-if="related?.length > 1"
                >
                    <button
                        class="show-toggle icon icon-only-icon"
                        :title="msgMore"
                        @click.stop.prevent="relatedOpen = !relatedOpen"
                        v-if="relatedOpen"
                    >
                        <app-icon icon="carbon:subtract" />
                        <span class="is-sr-only">{{ msgMore }}</span>
                    </button>
                    <button
                        class="show-toggle icon icon-only-icon"
                        :title="msgMore"
                        @click.stop.prevent="relatedOpen = !relatedOpen"
                        v-else
                    >
                        <app-icon icon="carbon:add" />
                        <span class="is-sr-only">{{ msgMore }}</span>
                    </button>
                </div>
            </div>
            <template v-if="relatedOpen">
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
                        >
                            <template v-if="f == 'media_url'">
                                <img :src="thumb(index)">
                            </template>
                            <template v-else>
                                {{ relatedItem?.attributes?.[f] || relatedItem?.meta?.[f] }}
                            </template>
                        </span>
                    </div>
                </template>
            </template>
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
            type: Object,
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
            this.truncated = this.text?.length <= 100 ? this.text : this.text?.substring(0, 100);
            if (Object.keys(this.schema).length) {
                const oneOf = this.schema?.['oneOf'] || null;
                if (oneOf && oneOf?.length > 1) {
                    const index = oneOf?.length > 1 ? 1 : 0;
                    const isObject = JSON.stringify(oneOf[index]) === '{}' || JSON.stringify(oneOf[index]) === 'true' || oneOf[index]?.['type'] === 'object';
                    this.renderAs = isObject ? 'object' : 'string';
                    return;
                }
                const isObject = JSON.stringify(this.schema) === '{}' || JSON.stringify(this.schema) === 'true' || this.schema?.['type'] === 'object';
                this.renderAs = isObject ? 'object' : 'string'
            }
        });
    },
    methods: {
        className() {
            return this.related?.length ? 'index-cell related-cell' : `index-cell ${this.prop}-cell`;
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
        showCopyIcon() {
            if (this.renderAs === 'object') {
                return false;
            }
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
    display: inline-block;;
}
div.index-cell > div.msg {
    color: forestgreen;
    font-family: monospace;
    font-style: italic;
}
div.index-cell div.related-container {
    display: flex;
}
div.index-cell div.related-item {
    display: flex;
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
