<template>
    <div class="secret">
        <div v-if="visible && val">
            <div>{{ val }}</div>
            <button
                class="button button-text-white is-width-auto"
                @click.prevent="reset(0)"
            >
                <app-icon icon="carbon:view-off-filled" />
            </button>
            <button
                class="button button-text-white is-width-auto"
                @click.prevent="copy()"
                v-if="!msg"
            >
                <app-icon icon="carbon:copy" />
            </button>
            <div
                v-text="msg"
                v-if="msg"
            />
        </div>
        <div v-if="!visible && val">
            <div>****************</div>
            <button
                class="button button-text-white is-width-auto"
                @click.prevent="reset(1)"
            >
                <app-icon icon="carbon:view-filled" />
            </button>
        </div>
        <div v-if="!val">
            ---
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'AppSecret',
    props: {
        val: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            visible: false,
            msg: '',
        };
    },

    methods: {
        copy() {
            navigator.clipboard.writeText(this.val);
            this.msg = t`copied in the clipboard`;
        },
        reset(v) {
            this.visible = v;
            this.msg = '';
        }
    },
}
</script>
