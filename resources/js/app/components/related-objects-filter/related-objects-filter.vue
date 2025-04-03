<template>
    <div class="related-objects-filter">
        <div class="filter-to-apply">
            <div
                v-for="relationName in Object.keys(filter)"
                :key="relationName"
            >
                <template v-if="filter?.[relationName]?.length">
                    <span class="tag relation">{{ tr(relationName) }}</span>
                    <template v-for="relatedId in filter[relationName]">
                        <div
                            class="tag"
                            :class="moduleClass(relatedId)"
                        >
                            [{{ relatedId }}] {{ objectsMap?.[relatedId]?.title }}
                            <a
                                class="unpick"
                                @click.prevent="pickout(relationName, relatedId)"
                            >
                                <app-icon
                                    icon="carbon:close"
                                    width="18"
                                    height="18"
                                />
                            </a>
                        </div>
                    </template>
                </template>
            </div>
        </div>
        <div class="selection-container">
            <div>
                <select
                    v-model="relation"
                    @change="changeRelation"
                >
                    <option
                        v-for="item in relations"
                        :key="item.name"
                        :value="item.name"
                    >
                        {{ tr(item.label) }}
                    </option>
                </select>
            </div>
            <div
                class="ml-05"
                v-if="relation"
            >
                <select v-model="objectType">
                    <option
                        v-for="item in objectTypes"
                        :key="item"
                        :value="item"
                    >
                        {{ tr(item) }}
                    </option>
                </select>
            </div>
            <div
                class="ml-05"
                v-if="relation && objectType != msgChooseObjectType"
            >
                <input
                    type="text"
                    v-model="searchText"
                >
                <button
                    :class="{ 'is-loading-spinner': loading }"
                    :disabled="loading || !searchText || searchText?.length < 3"
                    @click.prevent="search"
                >
                    <app-icon icon="carbon:search" />
                    <span class="ml-05">{{ msgSearch }}</span>
                </button>
            </div>
        </div>
        <div
            class="search-results"
            v-if="!loading && searchResults?.length"
        >
            <div
                v-for="item in searchResults"
                :key="item.id"
            >
                <button
                    class="picker button button-outlined is-width-auto"
                    @click.prevent="pickin(item)"
                    v-if="!filter?.[relation]?.includes(item?.id)"
                >
                    <app-icon icon="carbon:add" />
                    <span class="ml-05">
                        [{{ item.id }}] {{ item?.attributes?.title }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'RelatedObjectsFilter',
    props: {
        initialFilter: {
            type: Object,
            default: () => ({}),
        },
        relationsSchema: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            filter: {},
            loading: false,
            msgChooseObjectType: t`Choose object type`,
            msgChooseRelation: t`Choose relation`,
            msgFilterToApply: t`Filter to apply`,
            msgSearch: t`Search`,
            objectsMap: {},
            objectType: null,
            objectTypes: [],
            relation: null,
            relations: [],
            searchResults: [],
            searchText: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            if (Object.keys(this.filter).length) {
                this.$emit('edit-filter-relations', true);
            }
            for (const [key, value] of Object.entries(this.relationsSchema)) {
                this.relations.push({
                    label: value?.label || key,
                    name: key,
                    types: value?.types || [],
                });
            }
            this.relations.sort((a, b) => {
                if (a.label < b.label) return -1;
                if (a.label > b.label) return 1;
                return 0;
            });
            this.relations.unshift({
                label: this.msgChooseRelation,
                name: null,
                types: [],
            });
            this.relation = this.relations[0].name;
            if (this.initialFilter) {
                const relationNames = Object.keys(this.relationsSchema);
                const relationKeys = Object.keys(this.initialFilter).filter((key) => relationNames.includes(key));
                this.filter = relationKeys.reduce((acc, key) => {
                    acc[key] = this.initialFilter[key];
                    return acc;
                }, {});
            }
        });
    },
    methods: {
        changeRelation() {
            this.searchResults = [];
            this.objectTypes = this.relations.find((item) => item.name === this.relation)?.types || [];
            this.objectTypes.sort((a, b) => {
                if (a < b) return -1;
                if (a > b) return 1;
                return 0;
            });
            if (!this.objectTypes.includes(this.msgChooseObjectType)) {
                this.objectTypes.unshift(this.msgChooseObjectType);
            }
            this.objectType = this.msgChooseObjectType;
        },
        moduleClass(item) {
            const objectType = this.objectsMap?.[item]?.type || null;
            if (!objectType) {
                return '';
            }

            return `has-background-module-${objectType}`;
        },
        pickin(item) {
            if (!this.filter[this.relation]) {
                this.filter[this.relation] = [];
            }
            if (this.filter?.[this.relation]?.includes(item?.id)) {
                return;
            }
            this.filter[this.relation].push(item.id);
            this.updateFilter();
        },
        pickout(relationName, relatedId) {
            if (this.filter?.[relationName]) {
                this.filter[relationName] = this.filter[relationName].filter((id) => id !== relatedId);
                this.updateFilter();
            }
        },
        async search() {
            try {
                this.loading = true;
                const headers = new Headers({
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': BEDITA.csrfToken,
                });
                const options = {
                    credentials: 'same-origin',
                    headers,
                    method: 'GET',
                };
                const url = `/api/${this.objectType}?q=${this.searchText}&page=1&page_size=10`;
                const response = await fetch(url, options);
                const responseJson = await response.json();
                if (responseJson?.error) {
                    throw new Error(responseJson.error);
                }
                if (responseJson?.data) {
                    this.searchResults = responseJson.data;
                } else {
                    this.searchResults = [];
                }
                for (const item of this.searchResults) {
                    if (!this.objectsMap[item.id]) {
                        this.objectsMap[item.id] = {'type': item?.type || '', 'title': item?.attributes?.title || ''};
                    }
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        tr(item) {
            return BEDITA_I18N?.[item] || this.t(item) || item;
        },
        updateFilter() {
            this.$forceUpdate();
            this.$emit('update-filter-related', this.filter);
        },
    },
}
</script>
<style scoped>
div.related-objects-filter {
    display: flex;
    flex-direction: column;
    grid-gap: 0.5em;
    justify-content: space-between;
}
div.related-objects-filter div.selection-container {
    display: grid;
    grid-template-columns: 1fr 1fr 50%;
    grid-gap: 0.5em;
    width: 100%;
}
div.related-objects-filter div.search-results {
    display: grid;
    grid-template-columns: 1fr;
    grid-gap: 0.5em;
    width: 100%;
}
div.related-objects-filter button.picker {
    height: 20px;
    background-color: transparent;
    border: none;
    color: var(--color-text);
    cursor: pointer;
}
div.related-objects-filter div.filter-to-apply {
    display: block;
    width: 100%;
}
div.related-objects-filter .unpick {
    cursor: pointer;
    height: 100%;
}
div.related-objects-filter .unpick > svg {
    margin-left: 0.5rem;
    height: 100%;
}
div.related-objects-filter .relation {
    background-color: darkslategrey;
    color: white;
}
</style>
