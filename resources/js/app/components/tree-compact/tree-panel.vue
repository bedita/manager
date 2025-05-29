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
                        <h2>{{ msgNew }} {{ objectType }}</h2>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div class="container">
                        <form-field
                            v-for="field in fieldsRequired"
                            :key="field"
                            :field="fieldKey(field)"
                            :render-as="fieldType(field)"
                            :json-schema="schema?.properties?.[fieldKey(field)] || {}"
                            :is-uploadable="false"
                            :languages="languages"
                            :object-type="objectType"
                            :required="fieldsRequired?.includes(fieldKey(field))"
                            :val="obj?.attributes?.[fieldKey(field)] || schema?.[fieldKey(field)] || null"
                            v-model="formFieldProperties[objectType][fieldKey(field)]"
                            @error="fieldError"
                            @update="fieldUpdate"
                            @success="fieldSuccess"
                        />
                        <div>
                            <label>{{ msgParentFolder }} <span class="required">*</span> {{ parentId }}</label>
                            <div class="root">
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
                            <tree-node
                                v-for="(node, id) in tree"
                                :key="id"
                                :folders="folders"
                                :node="node"
                                :object-type="objectType"
                                :parent-folder-id="parentId"
                                :reference-object="obj"
                                @update-parent="updateParent"
                            />
                        </div>
                        <template v-for="field in fieldsOther">
                            <form-field
                                :key="field"
                                :field="fieldKey(field)"
                                :render-as="fieldType(field)"
                                :json-schema="schema?.properties?.[fieldKey(field)] || {}"
                                :is-uploadable="false"
                                :languages="languages"
                                :object-type="objectType"
                                :required="fieldsRequired?.includes(fieldKey(field))"
                                :val="obj?.attributes?.[fieldKey(field)] || schema?.[fieldKey(field)] || null"
                                v-model="formFieldProperties[objectType][fieldKey(field)]"
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
                        <div v-if="error && (error.length > 0 || Object.keys(error).length > 0)">
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
            error: {},
            fieldsMap: {},
            fieldsAll: [],
            fieldsInvalid: [],
            fieldsOther: [],
            fieldsRequired: [],
            formFieldProperties: {},
            msgClose: t`Close`,
            msgNew: t`New object in`,
            msgParentFolder: t`Parent folder`,
            msgRoot: t`/ (root)`,
            msgSave: t`Save`,
            parentId: null,
            saving: false,
            success: {},
        }
    },
    computed: {
        saveDisabled() {
            return this.fieldsInvalid.length > 0 || this.saving;
        },
    },
    mounted() {
        this.$nextTick(async () => {
            this.formFieldProperties[this.objectType] = {};
            this.fieldsRequired = BEDITA?.fastCreateFields?.[this.objectType]?.required || [];
            this.fieldsAll = BEDITA?.fastCreateFields?.[this.objectType]?.fields || ['title'];
            this.fieldsAll = this.fieldsAll.map(field => {
                if (typeof field === 'object') {
                    return Object.keys(field)[0];
                }
                return field;
            });
            this.fieldsOther = this.fieldsAll.filter(field => !this.fieldsRequired.includes(field));
            const fields = BEDITA?.fastCreateFields?.[this.objectType]?.fields || [];
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
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectType][f]);
            if (this.obj?.meta?.path) {
                const tmp = this.obj.meta.path.split('/').reverse();
                this.parentId = tmp[1] || null;
            }
        });
    },
    methods: {
        closePanel() {
            this.$emit('update:showPanel', false);
        },
        fieldError(field, val) {
            this.error[field] = val;
        },
        fieldUpdate(field, val) {
            this.formFieldProperties[this.objectType][field] = val;
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectType][f]);
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
        updateParent(id) {
            this.parentId = id;
        },
        async save() {
            console.log('saving...');
            try {
                this.saving = true;
                this.payload = Object.assign({}, this.formFieldProperties[this.objectType]);
                this.payload.objectType = this.objectType;
                if (this.obj?.id) {
                    this.payload.id = this.obj.id;
                }
                this.payload._changedParents = this.parentId;
                if (this.obj?.meta?.path) {
                    const tmp = this.obj.meta.path.split('/').reverse();
                    this.payload._originalParents = tmp[1] || null;
                }
                this.payload.relations = {
                    parent: {
                        replaceRelated: [
                            JSON.stringify({
                                id: this.parentId,
                                type: this.objectType,
                                meta: {
                                    relation: {
                                        menu: false,
                                        canonical: false
                                    }
                                }
                            })
                        ]
                    }
                };
                const url = `/${this.objectType}/save`;
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
