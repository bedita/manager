import { Icon } from '@iconify/vue2';
import { t } from 'ttag';

export default {
    template: `<div class="secret">
        <div v-if="visible && val">
            <div><: val :></div>
            <button class="button button-text-white is-width-auto" @click.prevent="reset(0)">
                <Icon icon="carbon:view-off-filled"></Icon>
            </button>
            <button v-if="!msg" class="button button-text-white is-width-auto" @click.prevent="copy()">
                <Icon icon="carbon:copy"></Icon>
            </button>
            <div v-if="msg" v-text="msg"></div>
        </div>
        <div v-if="!visible && val">
            <div>****************</div>
            <button class="button button-text-white is-width-auto" @click.prevent="reset(1)">
                <Icon icon="carbon:view-filled"></Icon>
            </button>
        </div>
        <div v-if="!val">---</div>



    </div>`,

    components: {
        Icon,
    },

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
