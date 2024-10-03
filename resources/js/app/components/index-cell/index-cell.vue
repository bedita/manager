<template>
    <div
        :class="className()"
        untitled-label="${t`Untitled`}"
        @mouseover="onMouseover()"
        @mouseleave="onMouseleave()"
    >
        {{ !msg ? truncated : '' }}
        <app-icon
            icon="carbon:copy"
            @click.stop.prevent="copy()"
            v-if="showCopyIcon()"
        />
        <div
            style="color: gray"
            v-text="msg"
            v-if="msg"
        />
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
            return `${this.prop}-cell`;
        },

        copy() {
            navigator.clipboard.writeText(this.text);
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
