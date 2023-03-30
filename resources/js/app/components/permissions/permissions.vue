<template>
    <div>
        <label v-for="role in roles" v-if="role.attributes.name != 'admin'">
            <input type="checkbox" :checked="objectRoles.includes(role.attributes.name)" />
            {{ role.attributes.name }}
            <permission :role="role.attributes.name" :object-roles="objectRoles" :user-roles="userRoles">
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
        objectRoles: [],
        userRoles: [],
    },

    data() {
        return {
            'roles': [],
        };
    },

    async mounted() {
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
    },
}
</script>
