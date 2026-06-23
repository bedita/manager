<template>
    <div class="input password required">
        <label for="password">{{ msgPassword }}</label>
        <div class="is-flex">
            <div class="is-expanded">
                <input
                    id="password"
                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;"
                    :type="type"
                    name="password"
                    required="required"
                    autocomplete="current-password"
                    aria-required="true"
                    v-model="password"
                    @keydown="onKeydown($event)"
                >
            </div>
            <div>
                <button
                    style="min-width: 32px; border-top-left-radius: 0; border-bottom-left-radius: 0;"
                    :disabled="!this.password || this.password.length == 0"
                    class="button button-primary"
                    @click.prevent.stop="toggleShow"
                >
                    <app-icon
                        icon="carbon:view-filled"
                        v-if="show"
                    />
                    <app-icon
                        icon="carbon:view-off-filled"
                        v-if="!show"
                    />
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'LoginPassword',
    data() {
        return {
            show: false,
            password: null,
            type: 'password',
            msgPassword: t`Password`,
        };
    },

    methods: {
        onKeydown(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                document.querySelector('form[action="/login"]').submit();
            }
        },
        toggleShow() {
            this.show = !this.show;
            this.type = this.show ? 'text' : 'password';
        },
    }
};
</script>
