<template>
    <div id="data-import">
        <div class="columns">
            <div class="column">
                <section class="fieldset">
                    <header>
                        <h2>{{ msgFileType }}</h2>
                    </header>
                    <div class="tab-container mt-05">
                        <div class="input" v-for="filter in filters">
                            <label>
                                <input type="radio" :id="filter.name" name="filter" v-model="activeFilter" :value="filter.value" />
                                {{ filter.text }}
                            </label>
                        </div>
                    </div>
                </section>
            </div>
            <div class="column">
                <section class="fieldset">
                    <header>
                        <h2>{{ msgOptions }}</h2>
                    </header>
                    <div class="tab-container mt-05">
                        <div v-for="filter in filters">
                            <div v-if="!filter.options">
                                <span class="has-text-gray-600">{{ msgNoOptions }}</span>
                            </div>
                            <template v-else>
                                <div class="mt-15" v-for="optionsData,optionsKey in filter.options" v-if="activeFilter == filter.value">
                                    <label>{{ optionsData.label }}
                                        <div v-if="optionsData.dataType === 'boolean'">
                                            <input type="checkbox" :name="`filter_options[${optionsKey}]`" :checked="filterOptions[filter.name][optionsKey] === true" v-model="filterOptions[filter.name][optionsKey]" />
                                        </div>
                                        <div v-if="optionsData.dataType === 'options'">
                                            <select :name="`filter_options[${optionsKey}]`" v-model="filterOptions[filter.name][optionsKey]">
                                                <option v-for="val,key in optionsData.values">{{ val }}</option>
                                            </select>
                                        </div>
                                        <div v-if="optionsData.dataType === 'text'">
                                            <input type="text" :name="`filter_options[${optionsKey}]`" v-model="filterOptions[filter.name][optionsKey]" />
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
            filterOptions: {},
            loading: false,
            msgChooseFile: t`Choose a file`,
            msgEmpty: t`Empty`,
            msgFileType: t`Import type`,
            msgImport: t`Import`,
            msgNoOptions: t`No options for this file type`,
            msgOptions: t`Options`,
        };
    },

    mounted() {
        this.activeFilter = this.filters?.[0].value || '';
        for (const filter of this.filters) {
            if (filter.options === undefined) {
                continue;
            }
            this.filterOptions[filter.name] = {};
            const keys = Object.keys(filter.options);
            for (const optionsKey of keys) {
                this.filterOptions[filter.name][optionsKey] = filter.options[optionsKey]?.defaultValue || '';
            }
        }
    },

    methods: {

        accept() {
            const filter = this.filters?.filter((v) => v.value === this.activeFilter)[0];
            const accept = filter?.accept || [];

            return accept.join(',');
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
