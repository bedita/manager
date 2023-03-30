<template>
    <span v-if="hasPermission(role)" v-title="help()">
        <Icon icon="carbon:locked" v-if="isLocked()"></Icon>
        <Icon icon="carbon:unlocked" v-if="!isLocked()"></Icon>
    </span>
</template>

<script>
import { Icon } from '@iconify/vue2';
import { t } from 'ttag';

export default {

    name: 'permission',

    components: {
        Icon,
    },

    props: {
        objectRoles: [],
        role: '',
        userRoles: [],
    },

    methods: {
        hasPermission() {
            return this.objectRoles.includes(this.role);
        },
        help() {
            return `${t('Your roles')}: ${this.userRoles.join(',')}`;
        },
        isLocked() {
            if (this.userRoles.includes('admin')) {
                return false;
            }

            return !this.userRoles.includes(this.role);
        },
    },
}
</script>
