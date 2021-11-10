import { t } from 'ttag';

export default {
    template: `<div class="secret">
        <div v-if="visible">
            <div>
                <: val :>
            </div>
        </div>
        <div v-if="!visible">
            <button class="button button-text-white is-width-auto" @click.prevent="visible = true">
                ${t`Show`}
            </button>
        </div>
    </div>`,

    props: {
        val: {
            type: String,
            default: ''
        }
    },

    data() {
        return {
            visible: false
        };
    }
}
