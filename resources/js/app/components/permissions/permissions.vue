<template>
    <div>
        <div v-if="canModify">{{ t('You can modify the permissions') }}</div>
        <div v-else>{{ t('You cannot modify the permissions') }}</div>
        <label v-for="role in roles" v-if="role.attributes.name != 'admin'">
            <input type="checkbox" :checked="objectRoles.includes(role.attributes.name)" :disabled="!canModify" />
            {{ role.attributes.name }}
            <permission :inherited="objectPerms?.inherited || false" :role="role.attributes.name" :object-roles="objectRoles" :user-roles="userRoles">
            </permission>
        </label>
    </div>
</template>

<script>
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
            'canModify': false,
            'objectRoles': [],
            'roles': [],
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
