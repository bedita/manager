<template>
    <div
        class="edit-children-params"
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
                        {{ relatedType }}
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
            >
                <div class="mb-1">
                    <span>
                        {{ msgPosition }}
                    </span>
                    <div class="position-params params">
                        <input
                            type="number"
                            name="position"
                            step="1"
                            v-model.number="position"
                        >
                        <span class="param">
                            <input
                                id="paramMenu"
                                :class="paramClass('menu')"
                                type="checkbox"
                                v-model="menu"
                            >
                            <label
                                :class="paramClass('menu')"
                                for="paramMenu"
                            >
                                {{ msgMenu }}
                            </label>
                        </span>
                        <span class="param" v-if="relatedType !== 'folders'">
                            <input
                                id="paramCanonical"
                                :class="paramClass('canonical')"
                                type="checkbox"
                                v-model="canonical"
                            >
                            <label
                                :class="paramClass('canonical')"
                                for="paramCanonical"
                            >
                                {{ msgCanonical }}
                            </label>
                        </span>
                    </div>
                </div>
                <div
                    class="mb-1"
                    v-for="(property, key) in properties"
                    :key="key"
                >
                    <PropertyField
                        :property="property"
                        :property-key="key"
                        v-model="editingParams[key]"
                    />
                </div>
            </form>

            <footer class="p-1">
                <button
                    class="has-background-info has-text-white"
                    @click.prevent="save()"
                >
                    {{ msgSave }}
                </button>
                <button
                    class="mx-1"
                    href="#"
                    @click="close()"
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
    name: 'EditChildrenParams',

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
            canonical: null,
            editingParams: {},
            menu: null,
            oldParams: {},
            objectColorClass: '',
            position: null,
            properties: [],
            relatedColorClass: '',
            relatedName: '',
            relatedStatus: '',
            relatedType: '',
            msgCancel: t`Cancel`,
            msgCanonical: t`Canonical`,
            msgInRelationWith: t`In relation with`,
            msgMenu: t`Menu`,
            msgPosition: t`Position`,
            msgRelationParameters: t`Relation parameters`,
            msgSave: t`Save`,
        }
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

    mounted() {
        this.$nextTick(() => {
            this.properties = this.schema?.attributes?.params || [];
            this.canonical = this.related?.meta?.relation?.canonical || null;
            this.menu = this.related?.meta?.relation?.menu || null;
            this.position = this.related?.meta?.relation?.position || '';
            for (let key in this.properties) {
                if (this.related.meta.relation.params && Object.keys(this.related.meta.relation.params).includes(key)) {
                    this.editingParams[key] = this.related.meta.relation.params[key];

                    continue;
                }
                this.editingParams[key] = this.properties[key].value || null;
            }
            this.objectColorClass = `has-background-module-${this.object.type}`;
            this.relatedColorClass = `has-background-module-${this.related.type}`;
            this.relatedName = this.related?.attributes?.title || t`(empty)`;
            this.relatedStatus = this.related?.attributes?.status || t`(empty)`;
            this.relatedType = this.related?.type || t`(not available)`;
        });
    },

    methods: {

        close() {
            PanelEvents.closePanel();
        },

        paramClass(name) {
            if (['canonical', 'menu'].includes(name)) {
                if (name === 'canonical' && this.canonical) {
                    return this.relatedColorClass;
                }
                if (name === 'menu' && this.menu) {
                    return this.relatedColorClass;
                }
            }
        },

        save() {
            const related = {...this.related};
            for (const key in this.editingParams) {
                if (this.editingParams[key] === '') {
                    this.editingParams[key] = null;
                }
            }
            related.meta.relation.position = this.position;
            related.meta.relation.params = this.editingParams;
            PanelEvents.sendBack('edit-params:save', related);
        },

        setInternalValues() {
            Object.assign(this.oldParams, this.related.meta.relation.params);
            Object.assign(this.editingParams, this.related.meta.relation.params);
            this.position = this.related.meta.relation.position;
        },
    },
}
</script>
<style>
.edit-children-params .position-params {
    display: grid;
    grid-template-columns: 80px 60px 80px;
    grid-gap: 0.5em;
    text-align: center;
    align-items: center;
}
.edit-children-params .params > .param > input {
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 0;
    opacity: 0;
    appearance: none;
}
.edit-children-params .params > .param > label {
    display: flex !important;
    align-items: center;
    margin: 0;
    padding: 0.25em 0.5em;
    color: #CCCCCC;
    line-height: 1 !important;
    border-radius: 2em;
    border: solid 1px currentColor;
    cursor: pointer;
}
</style>
