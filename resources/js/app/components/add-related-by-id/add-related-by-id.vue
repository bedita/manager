<template>
    <div class="add-related-by-id ml-1">
        <input
            type="text"
            placeholder="ID or uname"
            v-model="searchId"
        >
        <button
            class="button button-primary"
            :disabled="!searchId"
            @click.prevent="add"
        >
            <app-icon icon="carbon:add" />
            <span class="ml-05">{{ msgAdd }}</span>
        </button>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'AddRelatedById',
    props: {
        objectTypes: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            cache: {},
            msgAdd: t`Add`,
            msgAllowedTypes: t`Allowed types`,
            msgNotFound: t`Not found`,
            searchId: '',
        };
    },
    methods: {
        async add() {
            try {
                const obj = await this.fetchObject(this.searchId, this.objectTypes.join(','));
                this.$emit('found', obj);
            } catch (error) {
                BEDITA.error(`${this.msgNotFound}. ${this.msgAllowedTypes}: ${this.objectTypes.join(', ')}`);
            }
        },
        async fetchObject(id, types) {
            const cacheKey = `${id}-${types}`;
            if (this.cache[cacheKey]) {
                return this.cache[cacheKey];
            }
            const baseUrl = new URL(BEDITA.base).pathname;
            const response = await fetch(`${baseUrl}resources/get/${id}?filter[type]=${types}`, {
                credentials: 'same-origin',
                headers: {
                    accept: 'application/json',
                }
            });
            const responseJson = await response.json();
            const data = responseJson?.data || {};
            if (!data) {
                throw new Error('Object not found');
            }
            if (!this.objectTypes.includes(data?.type)) {
                throw new Error('Invalid object type');
            }
            this.cache[cacheKey] = data;

            return this.cache[cacheKey];
        },
    },
};
</script>
<style scoped>
div.add-related-by-id {
    display: flex;
    align-items: center;
    max-width: 200px;
}
div.add-related-by-id button {
    margin-left: 0.25rem;
}
</style>
