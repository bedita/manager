<template>
    <div class="form-field">
        <div :class="`input ${field} ${fieldType}`">
            <field-title
                :field="field"
                :required="required"
                :title="title"
            />
            <!-- file upload -->
            <file-upload
                :object-type="objectType"
                :object-form-reference="`fast-create-${objectType}`"
                @error="uploadError"
                @success="uploadSuccess"
                v-if="fieldType === 'file'"
            />
            <!-- text (date, datetime) --->
            <field-date
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :time="format == 'date-time'"
                :value="value"
                @change="update"
                v-if="fieldType === 'date'"
            />
            <!-- text (integer) --->
            <field-integer
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'integer'"
            />
            <!-- text (number) --->
            <field-number
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'number'"
            />
            <!-- date ranges -->
            <date-ranges-view
                :ranges="value"
                @update="update"
                v-if="fieldType === 'calendar'"
            />
            <!-- categories -->
            <object-categories
                :model-categories="val"
                :value="[]"
                @update="updateCategories"
                v-if="fieldType === 'categories'"
            />
            <!-- check (boolean) --->
            <field-checkbox
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'checkbox'"
            />
            <!-- select -->
            <field-select
                :id="`fast-create-${field}`"
                :items="enumItems(jsonSchema)"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'select'"
            />
            <!-- radio -->
            <field-radio
                :id="`fast-create-${field}`"
                :items="enumItems(jsonSchema)"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'radio'"
            />
            <!-- richtext -->
            <field-textarea
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :field="field"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'textarea'"
            />
            <!-- geo-coordinates -->
            <field-geo-coordinates
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'geo-coordinates'"
            />
            <!-- input text -->
            <field-string
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'string'"
            />
            <!-- captions -->
            <object-captions
                :object-type="objectType"
                :items="[]"
                :config="captionsConfig"
                :languages="languages"
                :readonly="false"
                @change="update"
                v-if="fieldType === 'captions'"
            />
            <!-- json -->
            <field-json
                :id="`fast-create-${field}`"
                :name="`fast-${objectType}-${field}`"
                :reference="`fast-create-${objectType}`"
                :value="value"
                @change="update"
                v-if="fieldType === 'json'"
            />
        </div>
    </div>
</template>
<script>
export default {
    name: 'FormField',
    props: {
        abstractType: {
            type: String,
            default: 'objects',
        },
        field: {
            type: String,
            required: true,
        },
        isUploadable: {
            type: Boolean,
            default: false,
        },
        jsonSchema: {
            type: Object,
            required: true,
        },
        languages: {
            type: Object,
            default: () => {},
        },
        objectType: {
            type: String,
            default: '',
        },
        renderAs: {
            type: String,
            default: '',
        },
        required: {
            type: Boolean,
            default: false,
        },
        val: {
            type: [Object, Array, String, Number, Boolean],
            default: () => null,
        },
    },
    data() {
        return {
            captionsConfig: {
                formats: {
                    'allowed': ['webvtt'],
                    'default': 'webvtt'
                },
            },
            fieldType: 'text',
            format: '',
            value: null,
        }
    },
    computed: {
        title() {
            if (this.renderAs === 'file' || (!this.renderAs && this.field === 'name' && this.abstractType === 'media' && this.isUploadable)) {
                return this.t('File');
            }
            return this.t(this.jsonSchema?.title || this.field);
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.value = this.val || this.jsonSchema?.default || null;
            this.update(this.value);
            this.fieldType = this.resetFieldType();
        });
    },
    methods: {
        enumItems(schema) {
            return schema?.enum || schema?.oneOf?.map(one => one?.enum)?.flat() || [];
        },
        resetFieldType() {
            if (this.renderAs) {
                return this.renderAs;
            }
            if (['captions', 'categories'].includes(this.field)) {
                return this.field;
            }
            if (this.field === 'date_ranges') {
                return 'calendar';
            }
            if (this.field === 'extra' || ['array','object'].includes(this.jsonSchema?.type) || this.jsonSchema?.oneOf?.filter(one => ['array','object'].includes(one?.type))?.length > 0) {
                return 'json';
            }
            if (this.field === 'name' && this.abstractType === 'media' && this.isUploadable) {
                return 'file';
            }
            const typeString = this.jsonSchema?.type === 'string' || this.jsonSchema?.oneOf?.filter(one => one?.type === 'string')?.length > 0;
            const typeDate = this.jsonSchema?.format === 'date' || this.jsonSchema?.oneOf?.filter(one => one?.format === 'date')?.length > 0;
            const typeDateTime = this.jsonSchema?.format === 'date-time' || this.jsonSchema?.oneOf?.filter(one => one?.format === 'date-time')?.length > 0;
            if (typeString && (typeDate || typeDateTime)) {
                this.format = this.jsonSchema?.format || this.jsonSchema?.oneOf.find(one => one.type === 'string' && ['date', 'date-time'].includes(one.format)).format;

                return 'date';
            }
            if (this.jsonSchema?.type === 'boolean' || this.jsonSchema?.oneOf?.filter(one => one?.type === 'boolean')?.length > 0) {
                return 'checkbox';
            }
            const isEnum = this.jsonSchema?.enum || this.jsonSchema?.oneOf?.filter(one => one?.enum?.length > 0)?.length > 0;
            const values = this.jsonSchema?.enum || this.jsonSchema?.oneOf?.map(one => one?.enum)?.flat();
            if (isEnum) {
                return values?.length > 3 ? 'select' : 'radio';
            }
            if (this.jsonSchema?.oneOf?.filter(one => one?.contentMediaType === 'text/html')?.length > 0) {
                return 'textarea';
            }
            if (this.jsonSchema?.type === 'integer' || this.jsonSchema?.oneOf?.filter(one => one?.type === 'integer')?.length > 0) {
                return 'integer';
            }
            if (this.jsonSchema?.type === 'number' || this.jsonSchema?.oneOf?.filter(one => one?.type === 'number')?.length > 0) {
                return 'number';
            }
            const isString = this.jsonSchema?.type === 'string' || this.jsonSchema?.oneOf?.filter(one => one?.type === 'string')?.length > 0;
            if (isString && this.field === 'coords') {
                return 'geo-coordinates';
            }

            return 'string';
        },
        update(value) {
            this.$emit('update', this.field, value);
        },
        updateCategories(value) {
            this.$emit('update', 'categories', JSON.stringify(value));
        },
        uploadError(e) {
            this.$emit('error', e)
        },
        uploadSuccess(data) {
            this.$emit('success', data)
        },
    },
}
</script>
<style scoped>
div.form-field {
    margin-bottom: 1em;
}
div.form-field .file {
    display: block !important;
}
</style>
