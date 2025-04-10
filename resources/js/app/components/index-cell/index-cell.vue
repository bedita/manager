<template>
    <div
        :class="className()"
        untitled-label="${t`Untitled`}"
        @mouseover="onMouseover()"
        @mouseleave="onMouseleave()"
    >
        <div
            v-html="truncated"
            v-if="!msg && truncated"
        />

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
                        {{ related?.[0]?.attributes?.[f] }}
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
                            {{ relatedItem?.attributes?.[f] }}
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
        settings: {
            type: Object,
            default: () => ({}),
        },
        prop: {
            type: String,
            default: '',
        },
        text: {
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
        untitledlabel: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            msg: '',
            msgMore: t`More`,
            relatedOpen: false,
            showCopy: false,
            truncated: '',
        };
    },
    async mounted() {
        this.$nextTick(() => {
            this.truncated = this.text?.length <= 100 ? this.text : this.text?.substring(0, 100);
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
            if (this.settings?.copy2clipboard !== true) {
                return false;
            }
            return !this.msg && this.showCopy;
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
</style>
