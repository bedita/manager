<template>
    <span v-if="hasPermission" v-title="title">
        <app-icon icon="carbon:locked" v-if="isLocked"></app-icon>
        <app-icon icon="carbon:unlocked" v-if="!isLocked"></app-icon>
        <app-icon icon="carbon:tree-view" v-if="inherited"></app-icon>
    </span>
</template>

<script>
import { t } from 'ttag';

export default {

    name: 'permission',

    props: {
        inherited: {
            type: Boolean,
            default: false,
        },
        objectRoles: {
            type: Array,
            default: () => ([]),
        },
        role: {
            type: String,
            default: '',
        },
        userRoles: {
            type: Array,
            default: () => ([]),
        },
    },

    data() {
        return {
            msgInherited: t`Inherited`,
        };
    },

    computed: {
        hasPermission() {
            return this.objectRoles.includes(this.role);
        },
        isLocked() {
            return !this.userRoles.includes('admin') && !this.userRoles.includes(this.role);
        },
        title() {
            return this.inherited ? this.msgInherited : '';
        },
    },
}
</script>
