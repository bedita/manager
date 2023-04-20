<template>
    <div>
        <div v-if="canModify" class="mb-05">
            <Icon icon="carbon:information" class="has-text-info"></Icon>
            <span class="ml-05 has-text-info">{{ msgYouCanModify }}. {{ msgYourRoles }}: {{ userRoles.join(',') }}</span>
        </div>
        <div v-else class="mb-05">
            <Icon icon="carbon:warning" class="has-text-danger"></Icon>
            <span class="ml-05 has-text-danger">{{ msgYouCannotModify }}. {{ msgYourRoles }}: {{ userRoles.join(',') }}</span>
        </div>
        <label v-for="role in roles" v-if="role.attributes.name != 'admin'">
            <input type="checkbox" name="permissions[]" :value="role.id" :checked="objectRoles.includes(role.attributes.name)" :disabled="!canModify" />
            {{ role.attributes.name }}
            <permission
                :inherited="objectPerms?.inherited || false"
                :role="role.attributes.name"
                :object-roles="objectRoles"
                :user-roles="userRoles">
            </permission>
        </label>
    </div>
</template>

<script>
import { t } from 'ttag';

export default {

    name: 'permissions',

    components: {
        Permission:() => import(/* webpackChunkName: "permission" */'app/components/permission/permission'),
    },

    props: {
        objectPerms: {
            type: Object,
            default: () => ([]),
        },
        userRoles: {
            type: Array,
            default: () => ([]),
        },
    },

    data() {
        return {
            canModify: false,
            objectRoles: [],
            roles: [],
            msgYouCanModify: t`You can modify the permissions`,
            msgYouCannotModify: t`You cannot modify the permissions`,
            msgYourRoles: t`Your roles`,
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
