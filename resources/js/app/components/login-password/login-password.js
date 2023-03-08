import { Icon } from '@iconify/vue2';
import { t } from 'ttag';

export default {
    template: `
    <div class="input password required">
        <label for="password">${t`Password`}</label>
        <div class="is-flex">
            <div class="is-expanded">
                <input
                    :type="type"
                    id="password"
                    name="password"
                    required="required"
                    autocomplete="current-password"
                    aria-required="true"
                    v-model="password"
                    @keydown="onKeydown($event)" />
            </div>
            <div>
                <button @click.prevent.stop="toggleShow" class="button button-primary" style="min-width: 32px;" :disabled="!this.password || this.password.length == 0">
                    <Icon icon="carbon:view-filled" v-if="show"></Icon>
                    <Icon icon="carbon:view-off-filled" v-if="!show"></Icon>
                </button>
            </div>
        </div>
    </div>
    `,

    components: {
        Icon,
    },

    data() {
        return {
            show: false,
            password: null,
            type: 'password',
        };
    },

    methods: {
        onKeydown(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                document.querySelector(`form[action="/login"]`).submit();
            }
        },
        toggleShow() {
            this.show = !this.show;
            this.type = this.show ? 'text' : 'password';
        },
    }
};
