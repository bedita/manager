import { t } from 'ttag';

export default {
    template: `<div class="secret">
        <div v-if="visible && val">
            <div><: val :></div>
            <button class="button button-text-white is-width-auto" @click.prevent="reset(0)">
                <icon-view-off-filled></icon-view-off-filled>
            </button>
            <button v-if="!msg" class="button button-text-white is-width-auto" @click.prevent="copy()">
                <icon-copy></icon-copy>
            </button>
            <div v-if="msg" v-text="msg"></div>
        </div>
        <div v-if="!visible && val">
            <div>****************</div>
            <button class="button button-text-white is-width-auto" @click.prevent="reset(1)">
                <icon-view-filled></icon-view-filled>
            </button>
        </div>
        <div v-if="!val">---</div>



    </div>`,

    components: {
        // icons
        IconCopy: () => import(/* webpackChunkName: "icon-copy" */'@carbon/icons-vue/es/copy/16.js'),
        IconViewFilled: () => import(/* webpackChunkName: "icon-view-filled" */'@carbon/icons-vue/es/view--filled/16.js'),
        IconViewOffFilled: () => import(/* webpackChunkName: "icon-view-off-filled" */'@carbon/icons-vue/es/view--off--filled/16.js'),
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
