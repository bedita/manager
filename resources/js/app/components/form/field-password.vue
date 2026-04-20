<template>
    <div class="field-password">
        <input
            :id="id"
            :name="name"
            :data-ref="reference"
            autocomplete="new-password"
            class="password"
            data-original-value=""
            :placeholder="getI18n('password', 'password')"
            type="password"
            value=""
            v-model="password"
            @change="change($event.target.value)"
        >
        <input
            :id="confirmPasswordField"
            :name="confirmPasswordField"
            autocomplete="new-password"
            class="password"
            data-original-value=""
            :placeholder="getI18n('confirm-password', 'confirm password')"
            type="password"
            value=""
            v-model="confirmPassword"
            @change="change($event.target.value)"
        >
        <span v-if="password && password === confirmPassword">
            <app-icon
                icon="carbon:checkmark"
                color="green"
            />
        </span>
        <span v-if="password !== confirmPassword">
            <app-icon
                icon="carbon:misuse"
                color="red"
            />
        </span>
    </div>
</template>
<script>
export default {
    name: 'FieldPassword',
    props: {
        id: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        reference: {
            type: String,
            default: ''
        },
        value: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            confirmPassword: '',
            confirmPasswordField: 'confirm-password',
            confirmPasswordTitle: 'Confirm Password',
            password: '',
        };
    },
    methods: {
        change(value) {
            if (this.confirmPassword && this.password !== this.confirmPassword) {
                return;
            }
            this.$emit('change', value);
        },
        getI18n(field, defaultValue) {
            return BEDITA_I18N?.[field] || defaultValue;
        },
    },
}
</script>
<style scoped>
div.field-password {
    display: grid;
    grid-template-columns: 1fr 1fr 50px;
    gap: 10px;
    align-items: center;
}
</style>
