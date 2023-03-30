<template>
    <div>
        <div v-if="canModify" class="mb-05">
            <Icon icon="carbon:information" class="info"></Icon>
            <span class="ml-05 info">{{ msgYouCanModify }}</span>
        </div>
        <div v-else class="mb-05">
            <Icon icon="carbon:warning" class="warning"></Icon>
            <span class="ml-05 warning">{{ msgYouCannotModify }}</span>
        </div>
        <label v-for="role in roles" v-if="role.attributes.name != 'admin'">
            <input type="checkbox" :checked="objectRoles.includes(role.attributes.name)" :disabled="!canModify" />
            {{ role.attributes.name }}
            <permission :inherited="objectPerms?.inherited || false" :role="role.attributes.name" :object-roles="objectRoles" :user-roles="userRoles">
            </permission>
        </label>
    </div>
</template>

<script>
import { t } from 'ttag';
import { Icon } from '@iconify/vue2';

export default {

    name: 'permissions',

    components: {
        Permission:() => import(/* webpackChunkName: "permission" */'app/components/permission/permission'),
        Icon,
    },

    props: {
        objectPerms: [],
        userRoles: [],
    },

    data() {
        return {
            canModify: false,
            objectRoles: [],
            roles: [],
            // i18n, @see https://github.com/ttag-org/ttag/issues/201
            msgYouCanModify: t`You can modify the permissions`,
            msgYouCannotModify: t`You cannot modify the permissions`,
        };
    },

    async mounted() {
        this.objectRoles = this.objectPerms?.roles || [];
        const headers = new Headers({
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': BEDITA.csrfToken,
        });
        const options = {
            credentials: 'same-origin',
            headers,
            method: 'GET',
        };
        const response = await fetch(`${BEDITA.base}/api/roles`, options);
        const responseJson = await response.json();
        this.roles = responseJson.data || [];
        this.canModify = this.userRoles.includes('admin') || this.roles.length === 0 || this.objectRoles.some(item => this.userRoles.includes(item));
    },
}
</script>
<style>
.info {
    color: hsl(204, 86%, 40%);
    font-size: small;
}
.warning {
    color: rgb(231, 200, 24);
    font-size: small;
}
</style>
