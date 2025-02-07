<template>
    <div class="form-field">
        <div :class="`input ${field} ${fieldType}`">
            <label :for="field">
                {{ title }}
                <b class="required" v-if="required">*</b>
            </label>
            <!-- file upload -->
            <template v-if="isFile">
                <file-upload
                    :object-type="objectType"
                    object-form-reference="[data-ref-fast-create]"
                    @error="uploadError"
                    @success="uploadSuccess"
                />
            </template>
            <!-- text (date, datetime) --->
            <template v-else-if="isDate">
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
            <template v-else-if="isInteger">
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
            <template v-else-if="isNumber">
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
            <template v-else-if="isDateRanges">
                <date-ranges-view
                    :ranges="value"
                    @update="update"
                />
            </template>
            <!-- categories -->
            <template v-else-if="isCategories">
                <object-categories
                    :model-categories="val"
                    :value="[]"
                    @update="updateCategories"
                />
            </template>
            <!-- check (boolean) --->
            <template v-else-if="isCheck">
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
            <template v-else-if="isSelect">
                <select
                    :id="`fast-create-${field}`"
                    data-ref-fast-create="1"
                    v-model="value"
                    @change="update($event.target.value)"
                >
                    <option
                        v-for="item in jsonSchema.enum"
                        :value="item"
                        :key="item"
                    >
                        {{ item }}
                    </option>
                </select>
            </template>
            <!-- radio -->
            <template v-else-if="isRadio">
                <label
                    v-for="item in jsonSchema.enum"
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
            <template v-else-if="isRichtext">
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
            <template v-else-if="isString">
                <input
                    :id="`fast-create-${field}`"
                    type="text"
                    data-ref-fast-create="1"
                    v-model="value"
                    @change="update($event.target.value)"
                >
            </template>
            <!-- captions -->
            <template v-else-if="isCaptions">
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
            <template v-else-if="isJson">
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
            <template v-else>
                -
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
        isCategories() {
            return this.field === 'categories';
        },
        isCaptions() {
            return this.field === 'captions';
        },
        isCheck() {
            return this.jsonSchema?.oneOf?.filter(one => one?.type === 'boolean')?.length > 0;
        },
        isDate() {
            return this.jsonSchema?.oneOf?.filter(one => one?.type === 'string' && ['date', 'date-time'].includes(one?.format))?.length > 0;
        },
        isDateRanges() {
            return this.field === 'date_ranges';
        },
        isFile() {
            return this.field === 'name' && this.abstractType === 'media' && this.isUploadable;
        },
        isInteger() {
            return this.jsonSchema?.oneOf?.filter(one => one?.type === 'integer')?.length > 0;
        },
        isJson() {
            return this.field === 'extra' || this.jsonSchema?.oneOf?.filter(one => ['array','object'].includes(one?.type))?.length > 0;
        },
        isNumber() {
            return this.jsonSchema?.oneOf?.filter(one => one?.type === 'number')?.length > 0;
        },
        isRadio() {
            return this.jsonSchema?.enum && this.jsonSchema?.enum?.length <= 3;
        },
        isRichtext() {
            return this.jsonSchema?.oneOf?.filter(one => one?.contentMediaType === 'text/html')?.length > 0;
        },
        isSelect() {
            return this.jsonSchema?.enum && this.jsonSchema?.enum?.length > 3;
        },
        isString() {
            if (this.jsonSchema?.type === 'string' && !this.jsonSchema?.enum) {
                return true;
            }
            return this.jsonSchema?.oneOf?.filter(one => one?.type === 'string')?.length > 0;
        },
        title() {
            if (this.field === 'name' && this.abstractType === 'media' && this.isUploadable) {
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
            if (this.isCategories) {
                this.fieldType = 'categories';
            } else if (this.isDateRanges) {
                this.fieldType = 'date_ranges';
            } else if (this.isDate) {
                this.format = this.jsonSchema?.oneOf.find(one => one.type === 'string' && ['date', 'date-time'].includes(one.format)).format;
            } else if (this.isCheck) {
                this.fieldType = 'checkbox';
            } else if (this.isSelect) {
                this.fieldType = 'select';
            } else if (this.isRadio) {
                this.fieldType = 'radio';
            } else if (this.isRichtext) {
                this.fieldType = 'textarea';
            } else if (this.isCaptions) {
                this.fieldType = 'captions';
            } else if (this.isJson) {
                this.fieldType = 'json';
            } else if (this.isFile) {
                this.fieldType = 'file';
            } else {
                this.fieldType = 'text';
            }
            this.toolbar = JSON.stringify(BEDITA?.richeditorByPropertyConfig?.[this.field]?.toolbar || null);
            this.loaded = true;
        });
    },
    methods: {
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
</style>
