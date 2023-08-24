import { t } from 'ttag';

export default {
    template: `<div class="secret">
        <div v-if="visible && val">
            <div><: val :></div>
            <button class="button button-text-white is-width-auto" @click.prevent="reset(0)">
                <app-icon icon="carbon:view-off-filled"></app-icon>
            </button>
            <button v-if="!msg" class="button button-text-white is-width-auto" @click.prevent="copy()">
                <app-icon icon="carbon:copy"></app-icon>
            </button>
            <div v-if="msg" v-text="msg"></div>
        </div>
        <div v-if="!visible && val">
            <div>****************</div>
            <button class="button button-text-white is-width-auto" @click.prevent="reset(1)">
                <app-icon icon="carbon:view-filled"></app-icon>
            </button>
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
