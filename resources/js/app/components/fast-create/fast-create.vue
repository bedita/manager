<template>
    <fieldset class="fast-create fieldset">
        <header
            class="tab"
            :class="{'open': open}"
            :open="open"
            @click="toggle"
        >
            <h2><span>{{ msgFastCreate }} <strong>"{{ relationLabel }}"</strong></span></h2>
        </header>
        <div
            class="fast-create-form-container"
            :open="open"
        >
            <div>
                <div
                    class="mb-1"
                    v-show="!autoType"
                >
                    <label for="objectType">{{ msgChooseType }}</label>
                    <select
                        v-model="objectType"
                        @change="changeType"
                    >
                        <option
                            v-for="item in items"
                            :value="item"
                            :key="item"
                        >
                            {{ t(capitalize(item)) }}
                        </option>
                    </select>
                </div>
                <template v-if="objectType">
                    <div
                        v-for="field in fields"
                        :key="field"
                        class="form-field-container"
                    >
                        <form-field
                            :key="reference"
                            :abstract-type="abstractType"
                            :field="fieldKey(field)"
                            :render-as="fieldType(field)"
                            :json-schema="schemasByType?.[objectType]?.properties?.[fieldKey(field)] || {}"
                            :is-uploadable="isUploadable"
                            :languages="languages"
                            :object-type="objectType"
                            :required="required?.includes(fieldKey(field))"
                            :val="schemasByType?.[objectType]?.[fieldKey(field)] || null"
                            v-model="formFieldProperties[objectType][fieldKey(field)]"
                            @error="err"
                            @update="update"
                            @success="success"
                        />
                    </div>
                </template>
                <div
                    class="form-field-container"
                    v-if="objectType"
                >
                    <button
                        :class="{ 'is-loading-spinner': loading }"
                        :disabled="saveDisabled"
                        @click.prevent="save"
                    >
                        <app-icon icon="carbon:save" />
                        <span class="ml-05">{{ msgSave }}</span>
                    </button>
                </div>
                <div
                    class="error mt-1"
                    v-if="error"
                >
                    <app-icon icon="carbon:error" />
                    <span class="ml-05">{{ error }}</span>
                </div>
                <div
                    class="mt-1"
                    v-if="message"
                >
                    <app-icon icon="carbon:checkmark" />
                    <span class="ml-05">{{ message }}</span>
                </div>
            </div>
        </div>
    </fieldset>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'FastCreate',
    components: {
        FormField: () => import(/* webpackChunkName: "form-field" */'app/components/fast-create/form-field'),
    },
    inject: ['getCSFRToken'],
    props: {
        autoType: {
            type: String,
            default: null,
        },
        fieldsByType: {
            type: Object,
            required: true
        },
        items: {
            type: Array,
            required: true
        },
        languages: {
            type: Object,
            default: () => {},
        },
        relationLabel: {
            type: String,
            required: true,
        },
        relationName: {
            type: String,
            required: true,
        },
        schemasByType: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            abstractType: 'objects',
            error: '',
            fields: [],
            fieldsMap: {},
            formFieldProperties: {},
            invalidFields: [],
            isUploadable: false,
            loading: false,
            message: '',
            msgChooseType: t`Choose a type`,
            msgCreated: t`Created`,
            msgFastCreate: t`Create new for`,
            msgSave: t`Save`,
            objectType: null,
            open: false,
            payload: {},
            required: [],
            reference: null,
        }
    },
    computed: {
        saveDisabled() {
            return this.invalidFields.length > 0 || this.loading;
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.isUploadable = BEDITA.uploadable.includes(this.objectType);
            this.abstractType = this.isUploadable ? 'media' : 'objects';
            if (this.autoType) {
                this.objectType = this.autoType;
                this.changeType();
            }
            if (this.objectType) {
                for (const field of this.fields) {
                    this.payload[this.fieldKey(field)] = this.schemasByType?.[this.objectType]?.[this.fieldKey(field)] || null;
                }
            }
            if (this.objectType) {
                this.formFieldProperties = {[this.objectType]: this.payload};
            }
            this.reference = Math.floor(Math.random() * 1000000);
        });
    },
    methods: {
        changeType() {
            this.formFieldProperties[this.objectType] = {};
            this.fields = [];
            this.fieldsMap = {};
            this.forceUploadable = false;
            this.required = [];
            this.payload = {};
            this.message = '';
            this.error = '';
            if (this.fieldsByType?.[this.objectType]?.fields) {
                const fields = this.fieldsByType[this.objectType].fields || [];
                let ff = fields;
                if (fields.constructor === Object) {
                    ff = Object.keys(fields);
                    this.fieldsMap = fields;
                }
                for (const item of ff) {
                    if (item.constructor === Object) {
                        const itemKey = Object.keys(item)[0];
                        this.fields.push(itemKey);
                        this.fieldsMap[itemKey] = item[itemKey];
                    } else {
                        this.fields.push(item);
                    }
                }
            }
            if (this.fieldsByType?.[this.objectType]?.required) {
                this.required = this.fieldsByType[this.objectType].required;
            }
            this.isUploadable = BEDITA.uploadable.includes(this.objectType);
            this.abstractType = this.isUploadable ? 'media' : 'objects';
            if (this.fields?.length === 0) {
                // set defaults
                this.fields = ['title', 'status'];
                this.fieldsMap = { title: 'string', status: 'radio' };
                this.required = ['title'];
                if (this.abstractType === 'media') {
                    this.isUploadable = true;
                    this.fields = ['title', 'status', 'name'];
                    this.fieldsMap = { title: 'string', status: 'radio', name: 'file' };
                    this.required = ['title', 'name'];
                }
            }
            this.invalidFields = this.required.filter(f => !this.payload[f]);
            if (this.objectType) {
                for (const field of this.fields) {
                    this.payload[this.fieldKey(field)] = this.schemasByType?.[this.objectType]?.[this.fieldKey(field)] || null;
                }
                this.formFieldProperties = {[this.objectType]: this.payload};
            }
        },
        err(val) {
            this.error = val;
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
        moduleName() {
            return this.objectType ? BEDITA_I18N?.[this.objectType] || this.objectType : BEDITA_I18N?.['objects'] || 'objects';
        },
        reset() {
            this.fields = [];
            this.required = [];
            this.payload = {};
            this.message = '';
            this.error = '';
            if (!this.autoType) {
                this.objectType = null;
            } else {
                this.changeType();
            }
            if (this.objectType) {
                for (const field of this.fields) {
                    this.payload[this.fieldKey(field)] = this.schemasByType?.[this.objectType]?.[this.fieldKey(field)] || null;
                }
            }
            if (this.objectType) {
                this.formFieldProperties = {[this.objectType]: this.payload};
            }
            // reset reference, to force re-render
            this.reference = Math.floor(Math.random() * 1000000);
        },
        async save() {
            try {
                this.loading = true;
                this.payload = Object.assign({}, this.formFieldProperties[this.objectType]);
                this.payload.objectType = this.objectType;
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
                this.reset();
                this.message = this.msgCreated;
                this.$emit('created', responseJson.data)
            } catch (error) {
                this.error = error;
            } finally {
                this.loading = false;
            }
        },
        success(objectData) {
            this.reset();
            this.message = this.msgCreated;
            this.$emit('created', [objectData]);
        },
        update(k,v) {
            this.formFieldProperties[this.objectType][k] = v;
            this.invalidFields = this.required.filter(f => !this.formFieldProperties[this.objectType][f]);
        },
        toggle() {
            this.open = !this.open;
        },
    },
}
</script>
<style scoped>
fieldset.fast-create {
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
}
fieldset.fast-create > header {
    cursor: pointer;
    font-size: 1rem;
    margin: 0rem 0.4rem;
}
fieldset.fast-create div.fast-create-form-container > div {
    list-style-type: none;
    outline: none;
    font-size: 1rem;
    margin: 1rem 0.4rem;
}
fieldset.fast-create div.fast-create-form-container > div::-webkit-details-marker {
    display: none;
}
fieldset.fast-create div.fast-create-form-container:is([open]) > div {
    flex-direction: column;
    /* margin: 1rem 0; */
}
fieldset.fast-create div.fast-create-form-container:not([open]) > div {
    display: none;
}
fieldset.fast-create .form-field-container {
    display: flex;
    flex-direction: row;
    margin: 0;
    width: 100%;
}
fieldset.fast-create input {
    width: 100%;
}
fieldset.fast-create .error {
    color: red;
}
</style>
