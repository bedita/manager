import { t } from 'ttag';

export default {
    template: `<div class="secret">
        <div v-if="visible && val">
            <div><: val :></div>
            <button class="button button-text-white is-width-auto icon-eye-off" @click.prevent="reset(0)"></button>
            <button v-if="!msg" class="button button-text-white is-width-auto icon-doc" @click.prevent="copy()"></button>
            <div v-if="msg" v-text="msg"></div>
        </div>
        <div v-if="!visible && val">
            <div>****************</div>
            <button class="button button-text-white is-width-auto icon-eye" @click.prevent="reset(1)"></button>
        </div>
        <div v-if="!val">---</div>
    </div>`,

    props: {
        val: '',
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
