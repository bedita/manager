/**
 * Filter Box View component
 *
 * allows to filter a list of objects with a text field, properties and a pagination toolbar
 *
 * <filter-box-view> component
 *
 * @prop {String} applyFilterLabel
 * @prop {String} resetFilterLabel
 * @prop {String} objectsLabel
 * @prop {String} pageSizesLabel
 * @prop {String} placeholder
 * @prop {Boolean} showFilterButtons
 * @prop {Object} initFilter
 * @prop {Object} relationTypes relation types available for relation (left/right)
 * @prop {Array} filterList custom filters to show
 * @prop {Object} pagination
 * @prop {String} configPaginateSizes
 */

import { DEFAULT_PAGINATION, DEFAULT_FILTER } from 'app/mixins/paginated-content';
import InputDynamicAttributes from 'app/components/input-dynamic-attributes';
import merge from 'deepmerge';

export default {
    components: {
        InputDynamicAttributes
    },

    template: `
    <form :name="objectsLabel" submit="applyFilter" action="noaction">
        <nav class="pagination has-text-size-smallest">

            <div class="count-items" v-if="pagination.count">
                <span><: pagination.count :> <: objectsLabel :></span>
            </div>


            <div class="filter-search">
                <span class="search-query">
                    <input type="text"
                        :placeholder="placeholder"
                        v-model="queryFilter.q"
                        @keyup.prevent.stop="onQueryStringChange"
                        @keyup.enter.prevent.stop="applyFilter"/>
                </span>

                <span v-if="rightTypes.length > 1" class="search-types">
                    <select v-model="queryFilter.filter.type">
                        <option value="" label="All Types"></option>
                        <option v-for="type in rightTypes"><: type :> </option>
                    </select>
                </span>

                <button v-show="showFilterButtons" name="applysearch" @click.prevent="applyFilter()"><: applyFilterLabel :></button>
                <button v-show="showFilterButtons" name="resetsearch" @click.prevent="resetFilter()"><: resetFilterLabel :></button>
            </div>

            <div class="filter-list">
                <div v-for="filter in filterList" class="filter-container input" :class="[filter.name, filter.type]">
                    <input type="hidden" :name="filter.name" :value="filter.value">

                    <span class="filter-name" :title="filterLabel(filter.name)"><: filter.name :></span>

                    <span v-if="filter.type === 'select' || filter.type === 'radio'">
                        <select v-model="queryFilter.filter[filter.name]" :id="filter.name">
                            <option value="">
                                All
                            </option>
                            <option v-for="option in filter.options" :name="option.name" :value="option.value">
                                <: option.text :>
                            </option>
                        </select>
                    </span>

                    <template v-else-if="filter.date">
                        <span class="datepicker-container">
                            <label>From:
                            <input-dynamic-attributes :value.sync="queryFilter.filter[filter.name]['gte']" :attrs="filter" :time="false" />
                            </label>
                        </span>
                        <span class="datepicker-container">
                            <label>To:
                            <input-dynamic-attributes :value.sync="queryFilter.filter[filter.name]['lte']" :attrs="filter" :time="false" />
                            </label>
                        </span>
                    </template>


                    <span v-else>
                        <input-dynamic-attributes :value.sync="queryFilter.filter[filter.name]" :attrs="filter"/>
                    </span>
                </div>
            </div>

            <div class="page-size" :class="pagination.count <= paginateSizes[0] && 'hide'">
                <span><: pageSizesLabel :>:</span>
                <select class="page-size-selector has-background-gray-700 has-border-gray-700 has-text-gray-200 has-text-size-smallest has-font-weight-light" v-model="pageSize">
                    <option v-for="size in paginateSizes"><: size :></option>
                </select>
            </div>

            <div class="pagination-buttons" :class="pagination.count <= pagination.page_size && 'hide'">
                <div class="pages-buttons full-layout" v-if="isFullPaginationLayout">
                    <button
                        v-for="i in pagination.page_count" :key="i"
                        v-bind:class="pagination.page == i? '' : 'is-dark'"
                        v-bind:style="pagination.page == i? 'pointer-events: none' : ''"
                        class="has-text-size-smallest" @click.prevent="changePage(i)"><: i :>
                    </button>
                </div>

                <div class="pages-buttons full-layout" v-if="!isFullPaginationLayout">
                    <!-- first page --->
                    <button v-if="pagination.page > 1" class="has-text-size-smallest" @click.prevent="changePage(1)"><: 1 :></button>

                    <!-- delimiter --->
                    <span v-if="pagination.page > 3" class="pages-delimiter">...</span>

                    <!-- prev page --->
                    <button v-if="pagination.page > 2" class="has-text-size-smallest" @click.prevent="changePage(pagination.page - 1)"><: pagination.page - 1:></button>

                    <!-- current page --->
                    <button class="is-dark current-page has-text-size-smallest" @click.prevent="changePage(pagination.page)"><: pagination.page :></button>

                    <!-- next page --->
                    <button v-if="pagination.page < pagination.page_count-1" class="has-text-size-smallest" @click.prevent="changePage(pagination.page + 1)"><: pagination.page + 1:></button>

                    <!-- delimiter --->
                    <span v-if="pagination.page < pagination.page_count-2" class="pages-delimiter">...</span>

                    <!-- last page --->
                    <button v-if="pagination.page < pagination.page_count" class="has-text-size-smallest" @click.prevent="changePage(pagination.page_count)"><: pagination.page_count :></button>
                </div>

            </div>

        </nav>
    </form>
    `,

    props: {
        applyFilterLabel: {
            type: String,
            default: "Apply"
        },
        resetFilterLabel: {
            type: String,
            default: "Reset"
        },
        objectsLabel: {
            type: String,
            default: "Objects"
        },
        pageSizesLabel: {
            type: String,
            default: "Size"
        },
        placeholder: {
            type: String,
            default: "Search"
        },
        showFilterButtons: {
            type: Boolean,
            default: true
        },
        initFilter: {
            type: Object,
            default: () => {
                return {
                    q: "",
                    filter: {
                        type: ""
                    }
                };
            }
        },
        relationTypes: {
            type: Object
        },
        filterList: {
            type: Array
        },
        pagination: {
            type: Object,
            default: () => DEFAULT_PAGINATION
        },
        configPaginateSizes: {
            type: String,
            default: "[10]"
        }
    },

    data() {
        return {
            queryFilter: {}, // QueryFilter Object
            timer: null,
            pageSize: this.pagination.page_size // pageSize value for pagination page size
        };
    },

    created() {
        // merge default filters with initFilter
        let customFilters = this.loadCustomFilters();
        this.queryFilter = merge.all([
            DEFAULT_FILTER,
            this.queryFilter,
            customFilters,
            this.initFilter
        ]);
    },

    computed: {
        paginateSizes() {
            return JSON.parse(this.configPaginateSizes);
        },

        /**
         * get relation's right object types
         *
         * @returns {Array} array of object types
         */
        rightTypes() {
            return (this.relationTypes && this.relationTypes.right) || [];
        },

        /**
         * check which navigation layout needs to be rendered
         *
         * @return {void}
         */
        isFullPaginationLayout() {
            return (
                this.pagination.page_count > 1 &&
                this.pagination.page_count <= 7
            );
        }
    },

    watch: {
        /**
         * watch initFilter and assign it to queryFilter
         *
         * @param {Object} value filter object
         *
         * @returns {void}
         */
        initFilter(value) {
            this.queryFilter = merge(this.queryFilter, value);
        },

        /**
         * watcher for pageSize variable, change pageSize and reload relations
         *
         * @param {Number} value
         *
         * @emits Event#filter-update-page-size
         *
         * @returns {void}
         */
        pageSize(value) {
            this.$emit("filter-update-page-size", this.pageSize);
        }
    },

    methods: {
        /**
         * trigger filter-objects event when query string has 3 or more carachter
         *
         * @emits Event#filter-objects
         */
        onQueryStringChange() {
            let queryString = this.queryFilter.q || "";

            clearTimeout(this.timer);
            if (queryString.length >= 3 || queryString.length == 0) {
                this.timer = setTimeout(() => {
                    this.$emit("filter-objects", this.queryFilter);
                }, 300);
            }
        },

        /**
         * load custom filters property names
         *
         * @returns {Object} filters' name
         */
        loadCustomFilters() {
            let filter = {};
            if (this.filterList) {
                this.filterList.forEach(
                    f => (filter[f.name] = f.date ? {} : "")
                );
            }

            return { filter: filter };
        },

        /**
         * apply filters
         *
         * @emits Event#filter-objects-submit
         */
        applyFilter() {
            this.$emit("filter-objects-submit", this.queryFilter);
        },

        /**
         * reset filters
         *
         * @emits Event#filter-reset
         */
        resetFilter() {
            this.$emit("filter-reset");
        },

        filterLabel(filterName) {
            return `filter name ${filterName}`;
        },

        /**
         * change page with index {index}
         *
         * @param {Number} index page number
         *
         * @emits Event#filter-update-current-page
         */
        changePage(index) {
            this.$emit("filter-update-current-page", index);
        }
    }
};
