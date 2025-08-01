<template>
    <div class="related-objects-filter">
        <template v-if="openPanel">
            <related-objects-panel
                :initial-filter="initialFilter"
                :relations-schema="relationsSchema"
                :show-panel="openPanel"
                @update:filter="updateData('filter', $event)"
                @update:objectsMap="updateData('objectsMap', $event)"
                @update:showPanel="updateData('openPanel', $event)"
            />
        </template>
        <template v-if="filter">
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

                <button
                    class="mt-1 button button-outlined is-small"
                    @click.prevent="openPanel = true"
                >
                    <app-icon icon="carbon:edit" />
                    <span class="ml-05">{{ msgEdit }}</span>
                </button>
            </div>
        </template>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'RelatedObjectsFilter',
    components: {
        'related-objects-panel': () => import('./related-objects-panel.vue'),
    },
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
            filter: null,
            msgEdit: t`Edit related objects`,
            objectsMap: null,
            openPanel: false,
        };
    },
    mounted() {
        this.$nextTick(() => {
            if (this.initialFilter) {
                const relationNames = Object.keys(this.relationsSchema);
                const relationKeys = Object.keys(this.initialFilter).filter((key) => relationNames.includes(key));
                this.filter = relationKeys.reduce((acc, key) => {
                    acc[key] = this.initialFilter[key];
                    return acc;
                }, {});
            } else {
                this.openPanel = true;
            }
        });
    },
    methods: {
        moduleClass(item) {
            const objectType = this.objectsMap?.[item]?.type || null;
            if (!objectType) {
                return '';
            }

            return `has-background-module-${objectType}`;
        },
        pickout(relationName, relatedId) {
            if (this.filter?.[relationName]) {
                this.filter[relationName] = this.filter[relationName].filter((id) => id !== relatedId);
                this.updateFilter();
            }
        },
        tr(item) {
            return BEDITA_I18N?.[item] || this.t(item) || item;
        },
        updateData(key, value) {
            this.$emit('edit-filter-relations', true);
            this[key] = value;
            if (key === 'filter') {
                this.updateFilter();
            }
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
</style>
