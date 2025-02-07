<template>
    <div class="form-field">
        <div :class="`input ${field} ${fieldType}`">
            <label :for="field">
                {{ title }}
                <b class="required" v-if="required">*</b>
            </label>
            <!-- file upload -->
            <template v-if="fieldType === 'file'">
                <file-upload
                    :object-type="objectType"
                    object-form-reference="[data-ref-fast-create]"
                    @error="uploadError"
                    @success="uploadSuccess"
                />
            </template>
            <!-- text (date, datetime) --->
            <template v-if="fieldType === 'date'">
                <input
                    :id="`fast-create-${field}`"
                    :name="`fast-${objectType}-${field}`"
                    data-ref-fast-create="1"
                    type="text"
                    date="true"
                    :time="format == 'date-time'"
                    :value="value"
                    v-datepicker
                    @change="update($event.target.value)"
                >
            </template>
            <!-- text (integer) --->
            <template v-if="fieldType === 'integer'">
                <input
                    :id="`fast-create-${field}`"
                    :name="`fast-${objectType}-${field}`"
                    data-ref-fast-create="1"
                    type="number"
                    :value="value"
                    @change="update($event.target.value)"
                >
            </template>
            <!-- text (number) --->
            <template v-if="fieldType === 'number'">
                <input
                    :id="`fast-create-${field}`"
                    :name="`fast-${objectType}-${field}`"
                    data-ref-fast-create="1"
                    type="number"
                    step="any"
                    :value="value"
                    @change="update($event.target.value)"
                >
            </template>
            <!-- date ranges -->
            <template v-if="fieldType === 'calendar'">
                <date-ranges-view
                    :ranges="value"
                    @update="update"
                />
            </template>
            <!-- categories -->
            <template v-if="fieldType === 'categories'">
                <object-categories
                    :model-categories="val"
                    :value="[]"
                    @update="updateCategories"
                />
            </template>
            <!-- check (boolean) --->
            <template v-if="fieldType === 'checkbox'">
                <input
                    :id="`fast-create-${field}`"
                    :name="`fast-${objectType}-${field}`"
                    data-ref-fast-create="1"
                    type="checkbox"
                    :checked="!!value"
                    @change="update($event.target.checked)"
                >
            </template>
            <!-- select -->
            <template v-if="fieldType === 'select'">
                <select
                    :id="`fast-create-${field}`"
                    data-ref-fast-create="1"
                    v-model="value"
                    @change="update($event.target.value)"
                >
                    <option
                        v-for="item in enumItems(jsonSchema)"
                        :value="item"
                        :key="item"
                    >
                        {{ item }}
                    </option>
                </select>
            </template>
            <!-- radio -->
            <template v-if="fieldType === 'radio'">
                <label
                    v-for="item in enumItems(jsonSchema)"
                    :key="item"
                >
                    <input
                        :id="`fast-create-${field}-${item}`"
                        type="radio"
                        data-ref-fast-create="1"
                        :name="`fast-${objectType}-${field}`"
                        :value="item"
                        v-model="value"
                        @change="update($event.target.value)"
                    >
                    {{ item }}
                </label>
            </template>
            <!-- richtext -->
            <template v-if="fieldType === 'textarea'">
                <textarea
                    :id="`fast-create-${field}`"
                    data-ref-fast-create="1"
                    :name="`fast-${objectType}-${field}`"
                    :data-toolbar="toolbar"
                    v-model="value"
                    v-richeditor
                    @change="update($event.target.value)"
                    v-if="loaded"
                />
            </template>
            <!-- input text -->
            <template v-if="fieldType === 'string'">
                <input
                    :id="`fast-create-${field}`"
                    type="text"
                    data-ref-fast-create="1"
                    v-model="value"
                    @change="update($event.target.value)"
                >
            </template>
            <!-- captions -->
            <template v-if="fieldType === 'captions'">
                <object-captions
                    :object-type="objectType"
                    :items="[]"
                    :config="captionsConfig"
                    :languages="languages"
                    :readonly="false"
                    @update="update"
                />
            </template>
            <!-- json -->
            <template v-if="fieldType === 'json'">
                <textarea
                    :id="`fast-create-${field}`"
                    class="json"
                    data-ref-fast-create="1"
                    :name="`fast-${objectType}-${field}`"
                    v-model="value"
                    v-jsoneditor
                    @change="updateJson($event.target.value)"
                />
            </template>
        </div>
    </div>
</template>
<script>
export default {
    name: 'FormField',
    components: {
        DateRangesView: () => import(/* webpackChunkName: "date-ranges-view" */'app/components/date-ranges-view/date-ranges-view'),
        FileUpload: () => import(/* webpackChunkName: "file-upload" */'app/components/file-upload/file-upload'),
        ObjectCategories: () => import(/* webpackChunkName: "object-categories" */'app/components/object-categories/object-categories'),
        ObjectCaptions: () => import(/* webpackChunkName: "object-captions" */'app/components/object-captions/object-captions'),
    },
    props: {
        abstractType: {
            type: String,
            default: 'objects',
        },
        field: {
            type: String,
            required: true,
        },
        forceType: {
            type: String,
            default: '',
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
            jsonEditorOptions: {
                mainMenuBar: true,
                mode: 'text',
                navigationBar: false,
                statusBar: false,
                readOnly: false,
            },
            loaded: false,
            toolbar: null,
            value: null,
        }
    },
    computed: {
        isNumber() {
            return this.forceType === 'number' || (!this.forceType && this.jsonSchema?.oneOf?.filter(one => one?.type === 'number')?.length > 0);
        },
        title() {
            if (this.forceType === 'file' || (!this.forceType && this.field === 'name' && this.abstractType === 'media' && this.isUploadable)) {
                return this.t('File');
            }
            return this.t(this.jsonSchema?.title || this.field);
        },
    },
    mounted() {
        this.$nextTick(() => {
            if (this.jsonSchema?.default) {
                this.value = this.jsonSchema.default;
                if (this.value) {
                    this.update(this.value);
                }
            }
            this.fieldType = this.resetFieldType();
            this.toolbar = JSON.stringify(BEDITA?.richeditorByPropertyConfig?.[this.field]?.toolbar || null);
            this.loaded = true;
        });
    },
    methods: {
        enumItems(schema) {
            return schema?.enum || schema?.oneOf?.map(one => one?.enum)?.flat() || [];
        },
        resetFieldType() {
            if (this.forceType) {
                return this.forceType;
            }
            if (['captions', 'categories'].includes(this.field)) {
                return this.field;
            }
            if (this.field === 'date_ranges') {
                return 'calendar';
            }
            if (this.field === 'extra' || this.jsonSchema?.oneOf?.filter(one => ['array','object'].includes(one?.type))?.length > 0) {
                return 'json';
            }
            if (this.field === 'name' && this.abstractType === 'media' && this.isUploadable) {
                return 'file';
            }
            if (this.jsonSchema?.oneOf?.filter(one => one?.type === 'string' && ['date', 'date-time'].includes(one?.format))?.length > 0) {
                this.format = this.jsonSchema?.oneOf.find(one => one.type === 'string' && ['date', 'date-time'].includes(one.format)).format;

                return 'date';
            }
            if (this.jsonSchema?.oneOf?.filter(one => one?.type === 'boolean')?.length > 0) {
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
            if (this.jsonSchema?.oneOf?.filter(one => one?.type === 'integer')?.length > 0) {
                return 'integer';
            }
            if (this.jsonSchema?.oneOf?.filter(one => one?.type === 'number')?.length > 0) {
                return 'number';
            }
            if (this.jsonSchema?.type === 'string' || this.jsonSchema?.oneOf?.filter(one => one?.type === 'string')?.length > 0) {
                return 'string';
            }

            return 'string';
        },
        update(value) {
            this.$emit('update', this.field, value);
        },
        updateCategories(value) {
            this.$emit('update', 'categories', JSON.stringify(value));
        },
        updateJson(value) {
            try {
                const parsed = JSON.parse(value);
                this.update(parsed);
            } catch (e) {
                this.$emit('error', e);
            }
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
div.form-field b.required {
    color: red;
}
div.form-field .file {
    display: block !important;
}
</style>
