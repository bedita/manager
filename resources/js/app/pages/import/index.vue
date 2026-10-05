<template>
    <div id="data-import">
        <div class="columns">
            <div class="column is-narrow">
                <section class="fieldset">
                    <header>
                        <h2>{{ msgFileType }}</h2>
                    </header>
                    <div class="tab-container mt-05">
                        <div class="input" v-for="filter in filters" :key="filter.name">
                            <label>
                                <input
                                    :id="filter.name"
                                    type="radio"
                                    name="filter"
                                    :value="filter.value"
                                    v-model="activeFilter"
                                    @change="onFilterChange(filter)"
                                >
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
                        <div>
                            <template v-if="Object.keys(activeFilterOptions).length === 0">
                                <p>{{ msgNoOptions }}</p>
                            </template>
                            <template v-else>
                                <div
                                    v-for="key in activeFilterOptionsKeys"
                                    class="mt-15"
                                    :key="key"
                                >
                                    <label>{{ activeFilterOptions[key].label }}
                                        <div v-if="activeFilterOptions[key].dataType === 'boolean'">
                                            <input
                                                type="checkbox"
                                                :name="`filter_options[${key}]`"
                                                v-model="filterOptions[activeFilterName][key]"
                                            >
                                        </div>
                                        <div v-if="activeFilterOptions[key].dataType === 'options'">
                                            <select
                                                :name="`filter_options[${key}]`"
                                                v-model="filterOptions[activeFilterName][key]"
                                            >
                                                <option
                                                    v-for="(val, optKey) in activeFilterOptions[key].values"
                                                    :key="optKey"
                                                    :value="val"
                                                >
                                                    {{ val }}
                                                </option>
                                            </select>
                                        </div>
                                        <div v-if="activeFilterOptions[key].dataType === 'text'">
                                            <input
                                                type="text"
                                                :name="`filter_options[${key}]`"
                                                v-model="filterOptions[activeFilterName][key]"
                                            >
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
                    <input
                        type="file"
                        class="file-input"
                        name="file"
                        :accept="accept()"
                        @change="onFileChange"
                    >
                    <span class="file-cta">
                        <app-icon icon="carbon:upload" />
                        <span class="ml-05">{{ msgChooseFile }}</span>
                    </span>
                    <span class="file-name">
                        <span
                            :data-empty-label="msgEmpty"
                            v-bind:title="fileName"
                        >
                            {{ fileName }}
                        </span>
                    </span>
                </label>
            </div>
        </section>

        <section class="fieldset">
            <button
                class="submit"
                :class="loading ? 'is-loading-spinner' : ''"
                @click.prevent="doImport"
                v-if="fileName"
            >
                {{ msgImport }}
            </button>
        </section>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ImportIndex',

    props: {
        filters: {
            type: Array,
            default: () => ([]),
        },
    },

    data() {
        return {
            activeFilter: '',
            activeFilterName: '',
            activeFilterOptions: {},
            activeFilterOptionsKeys: [],
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

    watch: {
        activeFilter(newValue) {
            const filter = this.filters?.find((f) => f.value === newValue);
            if (filter) {
                this.activeFilterName = filter.name;
                this.activeFilterOptions = filter.options || {};
                this.activeFilterOptionsKeys = Object.keys(filter.options || {});
            }
        },
    },

    mounted() {
        this.activeFilter = this.filters?.[0].value || '';
        this.activeFilterName = this.filters?.[0].name || '';
        this.activeFilterOptions = this.filters?.[0].options || {};
        this.activeFilterOptionsKeys = Object.keys(this.filters?.[0].options || {});
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

        onFilterChange(filter) {
            this.activeFilterName = filter.name;
            this.activeFilterOptions = filter.options || {};
            this.activeFilterOptionsKeys = Object.keys(filter.options || {});
        },
    },
}
</script>
