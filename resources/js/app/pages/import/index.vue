<template>
    <div id="data-import">
        <h1>{{ msgDataImport }}</h1>
        <div class="columns">
            <div class="column">
                <section class="fieldset">
                    <header class="tab open">
                        <h2>{{ msgFileType }}</h2>
                    </header>
                    <div class="tab-container mt-05">
                        <div class="input" v-for="filter in filters">
                            <label>
                                <input type="radio" name="filter" :checked="activeFilter == filter.text" @click.prevent="activeFilter = filter.text" :value="filter.value" />
                                {{ filter.text }}
                            </label>
                        </div>
                    </div>
                </section>
            </div>
            <div class="column">
                <section class="fieldset">
                    <header class="tab open">
                        <h2>{{ msgOptions }}</h2>
                    </header>
                    <div class="tab-container mt-05">
                        <div v-for="filter in filters">
                            <div v-if="!filter.options">
                                <span class="has-text-gray-600">{{ msgNoOptions }}</span>
                            </div>
                            <template v-else>
                                <div class="mt-15" v-for="optionsData,optionsKey in filter.options" v-show="activeFilter = filter.text">
                                    <label>{{ optionsData.label }}
                                        <div v-if="optionsData.dataType === 'boolean'">
                                            <input type="checkbox" :name="`filter_options[${optionsKey}]`" :checked="optionsData.defaultValue === true"/>
                                        </div>
                                        <div v-if="optionsData.dataType === 'options'">
                                            <select :name="`filter_options[${optionsKey}]`">
                                                <option v-for="val,key in optionsData.values" :value="key" :selected="optionsData.defaultValue == key ">{{ val }}</option>
                                            </select>
                                        </div>
                                        <div v-if="optionsData.dataType === 'text'">
                                            <input type="text" :name="`filter_options[${optionsKey}]`" :value="optionsData.defaultValue || ''" />
                                        </div>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <section class="fieldset">
            <div class="file has-name">
                <label class="file-label">
                    <input type="file" class="file-input" name="file" :accept="accept()" @change="onFileChange" />
                    <span class="file-cta">
                        <Icon icon="carbon:upload"></Icon>
                        <span class="ml-05">{{ msgChooseFile }}</span>
                    </span>
                    <span class="file-name">
                        <span :data-empty-label="msgEmpty" v-bind:title="fileName">{{ fileName }}</span>
                    </span>
                </label>
            </div>
        </section>

        <section class="fieldset">
            <button class="submit" :class="loading ? 'is-loading-spinner' : ''" @click.prevent="doImport" v-if="fileName">{{ msgImport }}</button>
        </section>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'import-index',

    props: {
        filters: {
            type: Array,
            default: () => ([]),
        },
    },

    data() {
        return {
            activeFilter: '',
            fileName: '',
            loading: false,
            msgChooseFile: t`Choose a file`,
            msgDataImport: t`Data Import`,
            msgEmpty: t`Empty`,
            msgFileType: t`File type`,
            msgImport: t`Import`,
            msgNoOptions: t`No options for this file type`,
            msgOptions: t`Options`,
        };
    },

    mounted() {
        this.activeFilter = this.filters?.[0].text || '';
    },

    methods: {

        accept() {
            const filter = this.filters?.filter((v) => v.text === this.activeFilter)[0];

            return filter.accept.join(',');
        },

        doImport() {
            this.loading = true;
            document.querySelector('form[id="form-import"]').submit();
        },

        onFileChange(e) {
            this.fileName = '';
            if (this.$helpers.checkMaxFileSize(e.target.files[0]) === false) {
                return;
            }

            this.fileName = e.target.files[0].name;
        },
    },
}
</script>
