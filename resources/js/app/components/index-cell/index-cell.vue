<template>
    <div
        :class="className()"
        untitled-label="${t`Untitled`}"
        @mouseover="onMouseover()"
        @mouseleave="onMouseleave()"
    >
        <div
            v-html="truncated"
            v-if="!msg"
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
            <app-icon icon="carbon:checkmark" color="green" />
            <span>{{ msg }}</span>
        </div>
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
        untitledlabel: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            msg: '',
            showCopy: false,
            truncated: '',
        };
    },
    async mounted() {
        this.truncated = this.text.length <= 100 ? this.text : this.text.substring(0, 100);
    },
    methods: {
        className() {
            return `index-cell ${this.prop}-cell`;
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
</style>
