import { t } from 'ttag';

export default {
    name: 'index-cell',

    template: `
    <div :class="className()" untitled-label="${t`Untitled`}" @mouseover="showCopy = true" @mouseleave="showCopy = false">
        <: !msg ? truncated : '' :>
        <span v-if="!msg && showCopy" class="is-width-auto icon-doc" @click.stop.prevent="copy()"></span>
        <div v-if="msg" v-text="msg" style="color: gray"></div>
    </div>
    `,

    props: {
        prop: '',
        text: '',
        untitledlabel: '',
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

        reset() {
            this.msg = '';
        },
    },
};
