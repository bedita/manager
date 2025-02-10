<template>
    <div class="fast-create">
        <div class="start" @click="reset(); clicked = true" v-if="!clicked">
            {{ msgFastCreate }}. {{ msgClickHereToStart }}
        </div>
        <fieldset v-else>
            <div v-show="!autoType">
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
                        v-model="payload[fieldKey(field)]"
                        @error="err"
                        @update="update"
                        @success="success"
                    />
                </div>
            </template>
            <div v-if="objectType">
                <button
                    :class="{'is-loading-spinner': loading}"
                    :disabled="loading || invalidFields?.length > 0"
                    @click.prevent="save"
                >
                    <app-icon icon="carbon:save" />
                    <span class="ml-05">{{ msgSave }}</span>
                </button>
                <button @click.prevent="reset()">
                    <app-icon icon="carbon:reset" />
                    <span class="ml-05">{{ msgCancel }}</span>
                </button>
            </div>
            <div class="error" v-if="error">
                <app-icon icon="carbon:error" />
                <span class="ml-05">{{ error }}</span>
            </div>
            <div v-if="message">
                <app-icon icon="carbon:checkmark" />
                <span class="ml-05">{{ message }}</span>
            </div>
        </fieldset>
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
            msgCancel: t`Cancel`,
            msgChooseType: t`Choose a type`,
            msgClickHereToStart: t`Click here to start`,
            msgCreated: t`Created`,
            msgFastCreate: t`Fast create`,
            msgSave: t`Save`,
            objectType: null,
            payload: {},
            required: [],
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.isUploadable = BEDITA.uploadable.includes(this.objectType);
            this.abstractType = this.isUploadable ? 'media' : 'objects';
            if (this.autoType) {
                this.objectType = this.autoType;
                this.changeType();
            }
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
            this.invalidFields = this.required.filter(f => !this.payload[f]);
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
            this.payload[k] = v;
            this.invalidFields = this.required.filter(f => !this.payload[f]);
        },
    },
}
</script>
<style scoped>
div.fast-create {
    margin-top: 1rem;
    margin-bottom: 1rem;
    display: flex;
    background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='white' stroke-width='3' stroke-dasharray='6' stroke-dashoffset='29' stroke-linecap='butt'/%3e%3c/svg%3e");
}
div.fast-create > div {
    margin: 1rem;
    display: inline-block;
}
div.fast-create > div.start {
    cursor: pointer;
    text-align: center;
    width: 100%;
}
div.fast-create > fieldset {
    margin: 1rem;
}
div.fast-create > fieldset > div {
    margin: 1rem;
}
div.fast-create > fieldset > div > label {
    display: block;
}
div.fast-create > fieldset > div > input {
    width: 100%;
}
div.fast-create > div > button {
    margin: 1rem;
}
div.fast-create div.error {
    color: red;
}
</style>
