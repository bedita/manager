import { t } from 'ttag';

export default {
    template: `
    <div class="input password required">
        <label for="password">${t`Password`}</label>
        <div class="is-flex">
            <div class="control is-expanded">
                <input
                    :type="type"
                    id="password"
                    name="password"
                    required="required"
                    autocomplete="current-password"
                    aria-required="true"
                    v-model="password" />
            </div>
            <div class="control">
                <button @click.prevent.stop="toggleShow" class="button button-primary" style="min-width: 32px;" :disabled="!this.password || this.password.length == 0">
                    <span :class="{ 'icon-eye-off': showPassword, 'icon-eye-1': !showPassword }"></span>
                </button>
            </div>
        </div>
    </div>
    `,

    data() {
        return {
            showPassword: false,
            password: null,
            type: 'password',
        };
    },

    methods: {
        toggleShow() {
            this.showPassword = !this.showPassword;
            this.type = this.showPassword ? 'text' : 'password';
        },
    }
};
