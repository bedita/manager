<template>
    <div class="related-objects-panel">
        <template v-if="showPanel">
            <div
                class="backdrop"
                style="display: block; z-index: 9998;"
                @click="closePanel()"
            />
            <aside
                class="main-panel-container on"
                custom-footer="true"
                custom-header="true"
            >
                <div class="main-panel fieldset">
                    <header class="mx-1 mt-1 tab tab-static unselectable">
                        <h2>{{ msgSearchRelated }}</h2>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click.prevent.stop="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div class="pcontainer">
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
                                    class="is-width-full"
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
                                <select
                                    class="is-width-full"
                                    v-model="objectType"
                                >
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
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                @click.prevent.stop="closePanel()"
                            >
                                <app-icon icon="carbon:close" />
                                <span class="ml-05">
                                    {{ msgClose }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </template>
    </div>
</template>
<script>
import { t } from 'ttag';
export default {
    name: 'RelatedObjectsPanel',
    props: {
        initialFilter: {
            type: Object,
            default: () => ({}),
        },
        relationsSchema: {
            type: Object,
            required: true,
        },
        showPanel: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            filter: {},
            loading: false,
            msgChooseObjectType: t`Choose object type`,
            msgChooseRelation: t`Choose relation`,
            msgClose: t`Close`,
            msgFilterToApply: t`Filter to apply`,
            msgSearch: t`Search`,
            msgSearchRelated: t`Search related objects`,
            objectsMap: {},
            objectType: null,
            objectTypes: [],
            openPanel: false,
            relation: null,
            relations: [],
            searchResults: [],
            searchText: null,
        }
    },

    mounted() {
        this.$nextTick(() => {
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
        closePanel() {
            this.$emit('update:filter', this.filter);
            this.$emit('update:objectsMap', this.objectsMap);
            this.$emit('update:showPanel', false);
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
div.related-objects-panel aside.main-panel-container {
    z-index: 9999;
}
div.related-objects-panel aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
div.related-objects-panel button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
div.related-objects-panel .pcontainer {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
div.related-objects-panel .pcontainer > div {
    display: flex;
    flex-direction: column;
}
div.related-objects-panel div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
}
div.related-objects-panel div.selection-container {
    display: grid;
    grid-template-columns: 1fr 1fr 50%;
    grid-gap: 0.5em;
    width: 100%;
}
div.related-objects-panel div.search-results {
    display: grid;
    grid-template-columns: 1fr;
    grid-gap: 0.5em;
    width: 100%;
}
div.related-objects-panel button.picker {
    height: 20px;
    background-color: transparent;
    border: none;
    color: var(--color-text);
    cursor: pointer;
}
div.related-objects-panel div.filter-to-apply {
    display: block;
    width: 100%;
}
div.related-objects-panel .unpick {
    cursor: pointer;
    height: 100%;
}
div.related-objects-panel .unpick > svg {
    margin-left: 0.5rem;
    height: 100%;
}
div.related-objects-panel .relation {
    background-color: darkslategrey;
    color: white;
}
</style>
