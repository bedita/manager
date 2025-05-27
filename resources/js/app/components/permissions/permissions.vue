<template>
    <div class="permissions">
        <div
            class="mb-05"
            v-if="canModify"
        >
            <app-icon
                icon="carbon:information"
                class="has-text-info"
            />
            <span class="ml-05 has-text-info">
                {{ msgYouCanModify }}.
                {{ msgYourRoles }}: {{ userRoles.join(',') }}.
            </span>
        </div>
        <div
            class="mb-05"
            v-else
        >
            <app-icon
                icon="carbon:warning"
                class="has-text-danger"
            />
            <span class="ml-05 has-text-danger">
                {{ msgYouCannotModify }}.
                {{ msgYourRoles }}: {{ userRoles.join(',') }}.
                <template v-if="readonlyRoles">{{ msgReadonlyRoles }}: {{ readonlyRoles.join(',') }}.</template>
            </span>
        </div>
        <template v-for="role in roles">
            <label
                :key="role.id"
                v-if="role.attributes.name != 'admin'"
            >
                <template v-if="!canModify">
                    <input
                        type="hidden"
                        name="permissions[]"
                        :value="role.id"
                        v-if="objectRoles.includes(role.attributes.name)"
                    >
                    <app-icon icon="carbon:checkmark" v-if="objectRoles.includes(role.attributes.name)" />
                    <app-icon icon="carbon:close" v-else />
                    {{ role.attributes.name }}
                </template>
                <template v-else>
                    <input
                        type="checkbox"
                        name="permissions[]"
                        :value="role.id"
                        :checked="objectRoles.includes(role.attributes.name)"
                    >
                    {{ role.attributes.name }}
                    <permission
                        :inherited="objectPerms?.inherited || false"
                        :role="role.attributes.name"
                        :object-roles="objectRoles"
                        :user-roles="userRoles"
                    />
                </template>
            </label>
        </template>
    </div>
</template>

<script>
import { t } from 'ttag';

export default {
    name: 'Permissions',
    components: {
        Permission:() => import(/* webpackChunkName: "permission" */'app/components/permission/permission'),
    },
    props: {
        objectPerms: {
            type: Object,
            default: () => ({}),
        },
        readonlyRoles: {
            type: Array,
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
            msgReadonlyRoles: t`Roles that cannot modify permissions`,
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
        const response = await fetch(`${BEDITA.base}/roles/list`, options);
        const responseJson = await response.json();
        this.roles = responseJson.data || [];
        if (this.userRoles.includes('admin') || this.roles?.length === 0 || this.objectRoles?.length === 0) {
            this.canModify = true;
            return;
        }
        this.canModify = this.objectRoles.some(item => !this.readonlyRoles.includes(item) && this.userRoles.includes(item));
    },
}
</script>
