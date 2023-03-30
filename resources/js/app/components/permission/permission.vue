<template>
    <span v-if="hasPermission(role)" v-title="help()">
        <Icon icon="carbon:locked" v-if="isLocked()"></Icon>
        <Icon icon="carbon:unlocked" v-if="!isLocked()"></Icon>
        <Icon icon="carbon:tree-view" v-if="inherited"></Icon>
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
        inherited: false,
        objectRoles: [],
        role: '',
        userRoles: [],
    },

    data() {
        return {
            // i18n, @see https://github.com/ttag-org/ttag/issues/201
            msgInherited: t`Inherited`,
            msgYourRoles: t`Your roles`,
        };
    },

    methods: {
        hasPermission() {
            return this.objectRoles.includes(this.role);
        },
        help() {
            const inherit = this.inherited ? this.msgInherited : '';

            return `${msgYourRoles}: ${this.userRoles.join(',')} ${inherit}`;
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
