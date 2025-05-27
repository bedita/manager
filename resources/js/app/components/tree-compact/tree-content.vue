<template>
    <div class="tree-content">
        <template v-if="obj && (createNew || editMode)">
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
                        <h2 v-if="createNew">{{ msgCreateNew }}</h2>
                        <h2 v-if="editMode">{{ msgEdit }}</h2>
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
                        <template v-for="field in fieldsOther">
                            <div
                                :key="field"
                                v-if="fieldKey(field) === 'date_ranges'"
                            >
                                <date-ranges-view
                                    :compact="true"
                                    :ranges="createNewDateRanges"
                                    @update="updateNewDateRanges"
                                />
                            </div>
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
                                v-else
                            />
                        </template>
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                :class="{'is-loading-spinner': saving}"
                                :disabled="saveDisabled"
                                @click.prevent="save"
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
                    </div>
                </div>
            </aside>
        </template>
        <header>
            <h2>
                <span><app-icon icon="carbon:document" /></span>
                <span
                    class="tag is-smallest mx-05"
                    :class="`has-background-module-${obj?.type}`"
                >
                    {{ obj?.type }}
                </span>
                <template v-if="canSave()">
                    <span
                        class="editable content"
                        :class="obj?.attributes?.status"
                        @click.prevent.stop="editMode = true"
                        @mouseover="hoverTitle=true"
                        @mouseleave="hoverTitle=false"
                    >
                        {{ truncate(obj?.attributes?.title, 80) }}
                        <template v-if="hoverTitle">
                            <app-icon
                                icon="ph:pencil-fill"
                                color="#00aaff"
                            />
                        </template>
                    </span>
                </template>
                <template v-else>
                    <span
                        class="content"
                        :class="obj?.attributes?.status"
                        v-title="obj?.attributes?.title"
                    >
                        {{ truncate(obj?.attributes?.title, 80) }}
                    </span>
                </template>
                <div class="object-info-container">
                    <object-info
                        border-color="transparent"
                        color="white"
                        :object-data="obj"
                    />
                </div>
                <a
                    :href="`/view/${obj?.id}`"
                    target="_blank"
                    class="mr-05"
                    v-title="msgOpenInNewTab"
                >
                    <app-icon icon="carbon:launch" />
                </a>
                <span class="modified">{{ $helpers.formatDate(obj?.meta?.modified) }}</span>
            </h2>
        </header>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'TreeContent',
    components: {
        FormField: () => import(/* webpackChunkName: "form-field" */'app/components/fast-create/form-field'),
        ObjectInfo: () => import(/* webpackChunkName: "object-info" */'app/components/object-info/object-info'),
    },
    props: {
        canSaveMap: {
            type: Object,
            required: true
        },
        languages: {
            type: Object,
            default: () => {},
        },
        obj: {
            type: Object,
            required: true
        },
        schema: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            createNew: false,
            editMode: false,
            error: {},
            fieldsMap: {},
            fieldsAll: [],
            fieldsInvalid: [],
            fieldsOther: [],
            fieldsRequired: [],
            formFieldProperties: {},
            hoverTitle: false,
            msgClose: t`Close`,
            msgCreateNew: t`Create new`,
            msgEdit: t`Edit`,
            msgOpenInNewTab: t`Open in new tab`,
            msgSave: t`Save`,
            msgUndo: t`Undo`,
            objectType: this.obj?.type || '',
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
        });
    },
    methods: {
        canSave() {
            return this.canSaveMap?.[this.obj?.type] || false;
        },
        closePanel() {
            this.createNew = false;
            this.editMode = false;
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
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
    },
}
</script>
<style scoped>
div.tree-content {
    padding-left: 0.5rem;
}
div.tree-content aside.main-panel-container {
    z-index: 9999;
}
div.tree-content aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
div.tree-content button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
div.tree-content .container {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
div.tree-content .container > div {
    display: flex;
    flex-direction: column;
}
div.tree-content div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
}
div.tree-content > header > h2 {
    display: flex;
    flex-direction: row;
    gap: 0.2rem;
    align-items: center;
    justify-content: start;
    border-bottom: dotted 0.1px silver;
}
div.tree-content > header > h2 > span.off, div.tree-content > header > h2 > span.draft {
    color: #737c81;
}
div.tree-content > header > h2 > span.content {
    font-size: 0.875rem;
}
div.tree-content > header > h2 > span.modified, div.tree-content > header > h2 > a {
    font-size: 0.7rem;
}
div.tree-content .editable:hover {
    cursor: pointer;
    color: #00aaff;
    text-decoration: underline;
}
div.tree-content div.object-info-container {
    margin-left: auto;
}
</style>
