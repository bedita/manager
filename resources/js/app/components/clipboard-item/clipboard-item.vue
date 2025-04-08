<template>
    <div class="clipboard-item">
        <label v-title="text" v-if="!msg">
            <button class="button is-small" @click.stop.prevent="copy()">
                <span class="mr-05">{{ label }}</span>
                <app-icon icon="carbon:copy"></app-icon>
            </button>
        </label>
        <label v-else>
            <button readonly="readonly" class="button is-small">
                <app-icon icon="carbon:checkmark"></app-icon>
            </button>
        </label>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ClipboardItem',

    props: {
        label: {
            type: String,
            default: '',
        },
        text: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            msg: '',
        };
    },

    methods: {
        copy() {
            navigator.clipboard.writeText(this.text);
            this.msg = t`Copied!`;
            setTimeout(() => this.reset(), 2000)
        },

        reset() {
            this.msg = '';
        },
    },
}
</script>
