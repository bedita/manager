<template>
    <div class="fast-create">
        <div
            class="backdrop"
            v-if="clicked"
        />
        <div>
            <button
                class="button button-primary is-width-auto"
                :disabled="loading || clicked"
                @click.prevent.stop="reset(); clicked = true"
            >
                <app-icon icon="carbon:add" />
                <span class="ml-05">{{ msgCreateObject }}</span>
            </button>
        </div>
        <aside
            class="main-panel-container"
            :class="clicked ? 'on' : ''"
            v-if="clicked"
        >
            <div class="main-panel">
                <div class="fast-create-form-container">
                    <header
                        class="mx-1 tab tab-static unselectable"
                        style="padding-left: 0px !important"
                    >
                        <h2>
                            <span class="mb-1"><strong>{{ msgFastCreate }} {{ moduleName() }}</strong></span>
                        </h2>
                        <span
                            class="close"
                            :title="msgClose"
                            @click.prevent="reset()"
                        >
                            <app-icon
                                icon="carbon:close"
                                class="hw-30"
                            />
                        </span>
                    </header>
                    <fieldset>
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
                            >
                                <form-field
                                    :abstract-type="abstractType"
                                    :field="fieldKey(field)"
                                    :render-as="fieldType(field)"
                                    :json-schema="schemasByType?.[objectType]?.properties?.[fieldKey(field)] || {}"
                                    :is-uploadable="isUploadable"
                                    :languages="languages"
                                    :object-type="objectType"
                                    :required="required?.includes(fieldKey(field))"
                                    :val="schemasByType?.[objectType]?.[fieldKey(field)] || null"
                                    v-model="formFieldProperties[fieldKey(field)]"
                                    @error="err"
                                    @update="update"
                                    @success="success"
                                />
                            </div>
                        </template>
                        <div v-if="objectType">
                            <button
                                :class="{ 'is-loading-spinner': loading }"
                                :disabled="saveDisabled"
                                @click.prevent="save"
                            >
                                <app-icon icon="carbon:save" />
                                <span class="ml-05">{{ msgSave }}</span>
                            </button>
                            <button @click.prevent="reset()">
                                <app-icon icon="carbon:close" />
                                <span class="ml-05">{{ msgClose }}</span>
                            </button>
                        </div>
                        <div
                            class="error"
                            v-if="error"
                        >
                            <app-icon icon="carbon:error" />
                            <span class="ml-05">{{ error }}</span>
                        </div>
                        <div v-if="message">
                            <app-icon icon="carbon:checkmark" />
                            <span class="ml-05">{{ message }}</span>
                        </div>
                    </fieldset>
                </div>
            </div>
        </aside>
    </div>
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
        schemasByType: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            abstractType: 'objects',
            clicked: false,
            error: '',
            fields: [],
            fieldsMap: {},
            invalidFields: [],
            isUploadable: false,
            loading: false,
            message: '',
            msgClose: t`Close`,
            msgChooseType: t`Choose a type`,
            msgCreated: t`Created`,
            msgCreateObject: t`Create object`,
            msgFastCreate: t`Fast create`,
            msgSave: t`Save`,
            objectType: null,
            payload: {},
            formFieldProperties: {},
            required: [],
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
            this.formFieldProperties = Object.assign({}, this.payload);
        });
    },
    methods: {
        changeType() {
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
            this.clicked = false;
            if (!this.autoType) {
                this.objectType = null;
            } else {
                this.changeType();
            }
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
            this.formFieldProperties[k] = v;
            this.invalidFields = this.required.filter(f => !this.formFieldProperties[f]);
        },
    },
}
</script>
<style scoped>
div.fast-create {
    display: inline-block;
}
div.fast-create > div.fast-create-form-container {
    display: flex;
    flex-direction: column;
    margin: 1rem 0;
    padding: 1rem 0;
}
div.fast-create > div.fast-create-form-container > div {
    margin: 0.5rem 1rem 0.5rem 1rem;
    display: inline-block;
}
div.fast-create > div.fast-create-form-container > div.start {
    cursor: pointer;
    text-align: center;
    width: 100%;
}
div.fast-create > div.fast-create-form-container > fieldset {
    margin: 1rem;
}
div.fast-create > div.fast-create-form-container > fieldset > div {
    margin: 1rem;
}
div.fast-create > div.fast-create-form-container > fieldset > div > label {
    display: block;
}
div.fast-create > div.fast-create-form-container > fieldset > div > input {
    width: 100%;
}
div.fast-create > div.fast-create-form-container > div > button {
    margin: 1rem;
}
div.fast-create > div.fast-create-form-container div.error {
    color: red;
}
div.fast-create > div.fast-create-form-container div.close {
    position: fixed;
    right: 0;
    cursor: pointer;
}
div.fast-create > div.fast-create-form-container span.close {
    cursor: pointer;
}
div.fast-create > div.backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: block;
    z-index: 1;
}
div.fast-create > div.fade-enter-active,
div.fast-create > div.fade-leave-active {
    transition: opacity .5s;
}
div.fast-create > div.fade-enter,
div.fast-create > div.fade-leave-to {
    opacity: 0;
}
div.fast-create > div.fade-enter-to,
div.fast-create > div.fade-leave {
    opacity: 0.75;
}
</style>
