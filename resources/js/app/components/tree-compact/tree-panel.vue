<template>
    <div class="tree-panel">
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
                        <template v-if="objectTypeForm">
                            <h2>{{ msgNewObjectIn }} {{ objectTypeForm }}</h2>
                        </template>
                        <template v-else>
                            <h2>{{ msgNewContent }}</h2>
                        </template>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div
                        class="container"
                        v-show="!objectTypeForm"
                    >
                        <label>{{ msgObjectType }} <span class="required">*</span></label>
                        <select
                            v-model="objectTypeForm"
                            @change="updateObjectType"
                        >
                            <option
                                v-for="item in objectTypes"
                                :key="item.value"
                                :value="item.value"
                            >
                                <span class="tag" :class="`has-background-module-${item.value}`">
                                    {{ item.label }}
                                </span>
                            </option>
                        </select>
                    </div>
                    <div
                        class="container"
                        v-if="objectTypeForm"
                    >
                        <form-field
                            v-for="field in fieldsRequired"
                            :key="field"
                            :field="fieldKey(field)"
                            :render-as="fieldType(field)"
                            :json-schema="schema?.properties?.[fieldKey(field)] || {}"
                            :is-uploadable="false"
                            :languages="languages"
                            :object-type="objectTypeForm"
                            :required="fieldsRequired?.includes(fieldKey(field))"
                            :val="obj?.attributes?.[fieldKey(field)] || schema?.[fieldKey(field)] || null"
                            v-model="formFieldProperties[objectTypeForm][fieldKey(field)]"
                            @error="fieldError"
                            @update="fieldUpdate"
                            @success="fieldSuccess"
                        />
                        <div>
                            <template v-if="objectTypeForm === 'folders'">
                                <label>{{ msgParentFolder }} <span class="required">*</span></label>
                            </template>
                            <template v-else>
                                <label>{{ msgPosition }} <span class="required">*</span></label>
                            </template>
                            <div
                                class="root"
                                v-if="objectTypeForm === 'folders'"
                            >
                                <label>
                                    <span>
                                        <input
                                            type="radio"
                                            name="position"
                                            v-model="parentId"
                                            @click="updateParent(null)"
                                        >
                                    </span>
                                    <span class="ml-05">
                                        {{ msgRoot }}
                                    </span>
                                </label>
                            </div>
                            <template v-if="objectTypeForm === 'folders'">
                                <tree-node
                                    v-for="(node, id) in tree"
                                    :key="id"
                                    :folders="folders"
                                    :node="node"
                                    :object-type="objectTypeForm"
                                    :parent-folder-id="parentId"
                                    :reference-object="obj"
                                    @update-parent="updateParent"
                                />
                            </template>
                            <template v-else>
                                <tree-node
                                    v-for="(node, id) in tree"
                                    :key="id"
                                    :folders="folders"
                                    :node="node"
                                    :object-type="objectTypeForm"
                                    :parent-folder-id="parentId"
                                    :reference-object="obj"
                                    @update-parent="updateParents"
                                />
                            </template>
                        </div>
                        <template v-for="field in fieldsOther">
                            <form-field
                                :key="field"
                                :field="fieldKey(field)"
                                :render-as="fieldType(field)"
                                :json-schema="schema?.properties?.[fieldKey(field)] || {}"
                                :is-uploadable="false"
                                :languages="languages"
                                :object-type="objectTypeForm"
                                :required="fieldsRequired?.includes(fieldKey(field))"
                                :val="obj?.attributes?.[fieldKey(field)] || schema?.[fieldKey(field)] || null"
                                v-model="formFieldProperties[objectTypeForm][fieldKey(field)]"
                                @error="fieldError"
                                @update="fieldUpdate"
                                @success="fieldSuccess"
                            />
                        </template>
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                :class="{'is-loading-spinner': saving}"
                                :disabled="saveDisabled"
                                @click="save"
                            >
                                <app-icon icon="carbon:save" />
                                <span class="ml-05">
                                    {{ msgSave }}
                                </span>
                            </button>
                            <button
                                class="button button-primary"
                                @click="closePanel()"
                            >
                                <app-icon icon="carbon:close" />
                                <span class="ml-05">
                                    {{ msgClose }}
                                </span>
                            </button>
                        </div>
                        <div v-if="error">
                            <p class="error">
                                {{ error }}
                            </p>
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
    name: 'TreePanel',
    components: {
        FormField: () => import(/* webpackChunkName: "form-field" */'app/components/fast-create/form-field'),
        TreeNode: () => import('./tree-node.vue'),
    },
    inject: ['getCSFRToken'],
    props: {
        canSaveMap: {
            type: Object,
            default: () => ({}),
        },
        folders: {
            type: Object,
            default: () => ({}),
        },
        languages: {
            type: Object,
            default: () => {},
        },
        obj: {
            type: Object,
            required: true
        },
        objectType: {
            type: String,
            required: true
        },
        schema: {
            type: Object,
            required: true
        },
        showPanel: {
            type: Boolean,
            default: false
        },
        tree: {
            type: Object,
            default: () => ({})
        },
    },
    data() {
        return {
            error: null,
            fieldsMap: {},
            fieldsAll: [],
            fieldsInvalid: [],
            fieldsOther: [],
            fieldsRequired: [],
            formFieldProperties: {},
            msgChooseObjectType: t`Choose object type`,
            msgClose: t`Close`,
            msgNewContent: t`New content`,
            msgNewObjectIn: t`New object in`,
            msgObjectType: t`Object type`,
            msgParentFolder: t`Parent folder`,
            msgPosition: t`Position`,
            msgRoot: t`/ (root)`,
            msgSave: t`Save`,
            objectTypeForm: null,
            objectTypes: [],
            parentId: null,
            parentIds: [],
            saving: false,
            success: {},
        }
    },
    computed: {
        inputName() {
            return this.objectTypeForm === 'folders' ? 'position' : 'position[]';
        },
        inputType() {
            return this.objectTypeForm === 'folders' ? 'radio' : 'checkbox';
        },
        saveDisabled() {
            return this.fieldsInvalid.length > 0 || this.saving;
        },
    },
    mounted() {
        this.$nextTick(async () => {
            this.objectTypeForm = this.objectType === 'choose' ? null : this.objectType;
            if (!this.objectTypeForm) {
                this.objectTypes = BEDITA?.concreteTypes || [];
                this.objectTypes = this.objectTypes.filter(type => this.canSaveMap[type]);
                // remove 'folders' from the list
                this.objectTypes = this.objectTypes.filter(type => type !== 'folders');
                this.objectTypes = this.objectTypes.map(type => {
                    return {
                        label: BEDITA_I18N?.[type] || this.$helpers.humanize(type),
                        value: type,
                    };
                });
                this.objectTypes = this.objectTypes.sort((a, b) => {
                    return a.label.localeCompare(b.label);
                });
                return;
            }
            this.updateObjectType();
        });
    },
    methods: {
        closePanel() {
            this.$emit('update:showPanel', false);
            this.objectTypeForm = null;
        },
        fieldError(field, val) {
            this.error[field] = val;
        },
        fieldUpdate(field, val) {
            this.formFieldProperties[this.objectTypeForm][field] = val;
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectTypeForm][f]);
        },
        fieldSuccess(field, val) {
            this.success[field] = val;
        },
        fieldKey(field) {
            return this.isNumeric(field) ? this.fieldsMap[field] : field;
        },
        fieldType(field) {
            return !this.isNumeric(field) ? this.fieldsMap[field] : null;
        },
        isNumeric(str) {
            if (typeof str != 'string') {
                return false;
            }
            return !isNaN(str) && !isNaN(parseFloat(str));
        },
        ll(field) {
            return BEDITA_I18N?.[field] || field;
        },
        updateObjectType() {
            this.formFieldProperties[this.objectTypeForm] = {};
            this.fieldsRequired = BEDITA?.fastCreateFields?.[this.objectTypeForm]?.required || [];
            this.fieldsAll = BEDITA?.fastCreateFields?.[this.objectTypeForm]?.fields || ['title'];
            this.fieldsAll = this.fieldsAll.map(field => {
                if (typeof field === 'object') {
                    return Object.keys(field)[0];
                }
                return field;
            });
            this.fieldsOther = this.fieldsAll.filter(field => !this.fieldsRequired.includes(field));
            const fields = BEDITA?.fastCreateFields?.[this.objectTypeForm]?.fields || [];
            let ff = fields;
            if (fields.constructor === Object) {
                ff = Object.keys(fields);
                this.fieldsMap = fields;
            }
            for (const item of ff) {
                if (item.constructor === Object) {
                    const itemKey = Object.keys(item)[0];
                    this.fieldsMap[itemKey] = item[itemKey];
                }
            }
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectTypeForm][f]);
            if (this.obj?.meta?.path) {
                const tmp = this.obj.meta.path.split('/').reverse();
                this.parentId = tmp[1] || null;
            }
        },
        updateParent(id) {
            this.parentId = id;
        },
        updateParents(id) {
            if (this.parentIds.includes(id)) {
                this.parentIds = this.parentIds.filter(pid => pid !== id);
            } else {
                this.parentIds.push(id);
            }
        },
        async save() {
            try {
                this.error = null;
                this.saving = true;
                this.payload = Object.assign({}, this.formFieldProperties[this.objectTypeForm]);
                this.payload.objectType = this.objectTypeForm;
                if (this.obj?.id) {
                    this.payload.id = this.obj.id;
                }
                this.payload._changedParents = this.parentId;
                if (this.obj?.meta?.path) {
                    const tmp = this.obj.meta.path.split('/').reverse();
                    this.payload._originalParents = tmp[1] || null;
                }
                if (this.objectTypeForm === 'folders') {
                    this.payload.relations = {
                        parent: {
                            replaceRelated: [
                                JSON.stringify({
                                    id: this.parentId,
                                    type: 'folders',
                                    meta: {
                                        relation: {
                                            menu: false,
                                            canonical: false
                                        }
                                    }
                                })
                            ]
                        },
                    };
                } else {
                    this.payload._changedParents = this.parentIds?.join(',') || null;
                    this.payload.relations = {
                        parents: {
                            replaceRelated: this.parentIds.map(
                                id => JSON.stringify({
                                    id,
                                    type: 'folders',
                                    meta: {
                                        relation: {
                                            menu: false,
                                            canonical: false
                                        }
                                    }
                                })
                            ),
                        },
                    };
                }
                const url = `/${this.objectTypeForm}/save`;
                const options = {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': this.getCSFRToken(),
                    },
                    body: JSON.stringify(this.payload),
                };
                const response = await fetch(url, options);
                const responseJson = await response.json();
                if (responseJson?.error) {
                    throw new Error(responseJson.error);
                }
                this.$emit('success', responseJson?.data?.[0]);
                this.closePanel();
            } catch (error) {
                this.error = error;
            } finally {
                this.saving = false;
            }
        },
    },
}
</script>
<style scoped>
div.tree-panel aside.main-panel-container {
    z-index: 9999;
}
div.tree-panel aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
div.tree-panel button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
div.tree-panel .container {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
div.tree-panel .container > div {
    display: flex;
    flex-direction: column;
}
div.tree-panel div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
}
div.tree-panel span.required {
    color: red;
}
div.tree-panel div.root > label {
    display:flex;
    align-items:center;
    direction:column;
    cursor: pointer;
}
div.tree-panel div.root > label > span {
    display: flex;
    align-self: center;
    cursor: pointer;
}
</style>
