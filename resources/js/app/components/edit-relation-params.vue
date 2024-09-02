<template>
    <div
        class="edit-relation"
        v-if="relationName"
    >
        <section>
            <header class="mx-1 mt-1 mb-1 tab unselectable">
                <h2><span>{{ msgRelationParameters }} - {{ relationLabel }}</span></h2>
            </header>

            <div class="mx-1">
                <p class="mb-05">
                    {{ relatedName }}
                </p>
                <div>
                    <span
                        class="tag"
                        :class="relatedColorClass"
                    >
                        {{ related?.type }}
                    </span>
                    <span class="tag">{{ relatedStatus }}</span>
                </div>

                <p class="mt-1 mb-05">{{ msgInRelationWith }} “{{ object?.attributes?.title }}”</p>
                <div>
                    <span
                        class="tag"
                        :class="objectColorClass"
                    >
                        {{ object?.type }}
                    </span>
                    <span class="tag">
                        {{ object?.attributes?.status }}
                    </span>
                </div>
            </div>

            <form
                class="mt-2 mx-1"
                ref="paramsForm"
                @change="checkParams()"
                @keyup="checkParams()"
            >
                <div class="mb-1">
                    <label>
                        {{ msgPriority }}
                        <input
                            class="input-narrow"
                            type="number"
                            step="1"
                            v-model.number="priority"
                        >
                    </label>
                </div>

                <div
                    class="mb-1"
                    v-for="(param, key) in schema"
                    :key="key"
                >
                    <PropertyField
                        :property="param"
                        :property-key="key"
                        v-model="editingParams[key]"
                    />
                </div>
            </form>

            <footer class="p-1">
                <button
                    :disabled="!isModified"
                    class="has-background-info has-text-white"
                    @click.prevent="saveParams()"
                >
                    {{ msgSave }}
                </button>
                <button
                    class="mx-1"
                    href="#"
                    @click="closeParamsView()"
                >
                    {{ msgCancel }}
                </button>
            </footer>
        </section>
    </div>
</template>
<script>
import { PanelEvents } from 'app/components/panel-view';
import PropertyField from 'app/components/edit-relation-param';
import { t } from 'ttag';

export default {
    name: 'EditRelationParams',

    components: {
        PropertyField,
    },

    props: {
        relationName: {
            type: String,
            default: '',
        },
        relationLabel: {
            type: String,
            default: '',
        },
        object: {
            type: Object,
            default: () => {},
        },
        related: {
            type: Object,
            default: () => {},
        },
        schema: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            oldParams: {},
            editingParams: {},
            priority: null,
            isModified: false,
            msgCancel: t`Cancel`,
            msgInRelationWith: t`In relation with`,
            msgPriority: t`Priority`,
            msgRelationParameters: t`Relation parameters`,
            msgSave: t`Save`,
        }
    },

    computed: {
        relatedStatus() {
            return this.related.attributes.status;
        },

        relatedType() {
            let type = '(not available)';
            if (this.related) {
                type = this.related.type;
            }

            return type;
        },

        relatedName() {
            let name = '(empty)';
            if (this.related) {
                name = this.related.attributes.title;
            }

            return name;
        },

        objectColorClass() {
            return `has-background-module-${this.object.type}`
        },

        relatedColorClass() {
            return `has-background-module-${this.related.type}`
        },
    },

    watch: {
        related: {
            handler: function(object) {
                if (object) {
                    this.setInternalValues();
                }
            },
            deep: true,
            immediate: true,
        }
    },

    methods: {
        setInternalValues() {
            Object.assign(this.oldParams, this.related.meta.relation.params);
            Object.assign(this.editingParams, this.related.meta.relation.params);
            this.priority = this.related.meta.relation.priority;
        },

        saveParams() {
            // internal prop are binded with the external
            if (Object.keys(this.editingParams).length) {
                this.related.meta.relation.params = this.editingParams;
            } else {
                delete this.related.meta.relation.params;
            }
            this.related.meta.relation.priority = this.priority;

            PanelEvents.sendBack('edit-params:save', this.related );
        },

        closeParamsView() {
            PanelEvents.closePanel();
        },

        checkParams() {
            this.isModified = !!Object.keys(this.editingParams).filter((index) => {
                return this.editingParams[index] !== this.oldParams[index];
            }).length || this.related.meta.relation.priority !== this.priority;
        },
    },
}
</script>
