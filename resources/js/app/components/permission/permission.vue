<template>
    <span v-if="hasPermission()" v-title="title()">
        <Icon icon="carbon:locked" v-if="isLocked()"></Icon>
        <Icon icon="carbon:unlocked" v-if="!isLocked()"></Icon>
        <Icon icon="carbon:tree-view" v-if="inherited"></Icon>
    </span>
</template>

<script>
import { t } from 'ttag';

export default {

    name: 'permission',

    props: {
        inherited: false,
        objectRoles: [],
        role: '',
        userRoles: [],
    },

    data() {
        return {
            // i18n, @see https://github.com/ttag-org/ttag/issues/201
            msgInherited: t`Inherited`,
        };
    },

    methods: {
        hasPermission() {
            return this.objectRoles.includes(this.role);
        },
        isLocked() {
            if (this.userRoles.includes('admin')) {
                return false;
            }

            return !this.userRoles.includes(this.role);
        },
        title() {
            return this.inherited ? this.msgInherited : '';
        },
    },
}
</script>
