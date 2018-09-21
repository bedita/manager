/**
 * Filter Box View component
 *
 * allows to filter a list of objects with a text field and a pagination toolbar
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
 * @prop {Object} pagination
 * @prop {String} configPaginateSizes
 */

import { DEFAULT_PAGINATION, DEFAULT_FILTER } from 'app/mixins/paginated-content';

export default {
    template:
    `
        <nav class="pagination has-text-size-smallest">

            <div class="count-items" v-if="pagination.count">
                <span><: pagination.count :> <: objectsLabel :></span>
            </div>

            <div class="filter-search">
                <span class="search-query">
                    <input type="text" :placeholder="placeholder" v-model="filter" @keyup.enter.prevent.stop="applyFilter()"/>
                </span>

                <span v-if="rightTypes.length > 1" class="search-types">
                    <select v-model="filterType">
                        <option value="" label="All Types"></option>
                        <option v-for="type in rightTypes"><: type :> </option>
                    </select>
                </span>

                <button v-show="showFilterButtons" name="applysearch" @click.prevent="applyFilter()"><: applyFilterLabel :></button>
                <button v-show="showFilterButtons" name="resetsearch" @click.prevent="resetFilter()"><: resetFilterLabel :></button>
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
    `,

    props: {
        applyFilterLabel: {
            type: String,
            default: 'Apply'
        },
        resetFilterLabel: {
            type: String,
            default: 'Reset',
        },
        objectsLabel: {
            type: String,
            default: 'Objects',
        },
        pageSizesLabel: {
            type: String,
            default: 'Size',
        },
        placeholder: {
            type: String,
            default: 'Search',
        },
        showFilterButtons: {
            type: Boolean,
            default: true,
        },
        initFilter: {
            type: Object,
            default: () => {
                return {
                    q: '',
                    filter: {
                        type: '',
                    }
                }
            },
        },
        relationTypes: {
            type: Object,
        },
        pagination: {
            type: Object,
            default: () => DEFAULT_PAGINATION,
        },
        configPaginateSizes: {
            type: String,
            default: '[10]',
        },
    },

    data() {
        return {
            filter: '', // Text string filter
            filterType: '', // Object type filter
            queryFilter: DEFAULT_FILTER, // QueryFilter Object
            timer: null,

            pageSize: this.pagination.page_size, // pageSize value for pagination page size
        }
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
            return this.relationTypes && this.relationTypes.right || [];
        },

        /**
         * check which navigation layout needs to be rendered
         *
         * @return {void}
         */
        isFullPaginationLayout() {
            return this.pagination.page_count > 1 && this.pagination.page_count <= 7;
        }
    },

    watch: {
        /**
         * watch initFilter and assign it to queryFilter, filter, filterType
         *
         * @param {Object} value filter object
         *
         * @returns {void}
         */
        initFilter(value) {
            Object.assign(this.queryFilter, this.queryFilter, value);

            this.filter = value.q;
            this.filterType = value.filter.type;
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
            this.$emit('filter-update-page-size', this.pageSize);

        },

        /**
         * watcher for text filter
         * if value is more than 3 chars, emits a filter-objects event with queryFilter as params
         *
         * @param {String} value The filter string
         *
         * @emits Event#filter-objects
         *
         * @return {void}
         */
        filter(value) {
            this.filter = value;
            this.queryFilter.q = this.filter;

            clearTimeout(this.timer);
            if (value.length >= 3 || value.length == 0) {
                this.timer = setTimeout(() => {
                    this.$emit('filter-objects', this.queryFilter);
                }, 300);
            }
        },

        /**
         * watcher for object type filter
         * emits a filter-objects event with queryFilter as params
         *
         * @param {String} value The filter object type
         *
         * @emits Event#filter-objects
         *
         * @return {void}
         */
        filterType(value) {
            this.filterType = value;
            this.queryFilter.filter.type = this.filterType;

            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.$emit('filter-objects', this.queryFilter);
            }, 300);
        },

        /**
         * watch initFilter and set filter accordingly
         */
        initFilter: {
            deep: true,
            immediate: true,
            handler: function(value) {
                this.filter = value && value.q || '';
            }
        }
    },

    mounted() {
        // merge default filters with initFilter
        Object.assign(this.queryFilter, this.queryFilter,this.initFilter);

        /**
         * init filter from queryFilter
         */
        if (this.queryFilter !== undefined) {
            this.filter = this.queryFilter.q || '';
            this.filterType = this.queryFilter.filter.type || '';
        }
    },

    methods: {
        /**
         * apply filters
         *
         * @emits Event#filter-objects-submit
         */
        applyFilter() {
            this.$emit('filter-objects-submit', this.queryFilter);
        },

        /**
         * reset filters
         *
         * @emits Event#filter-reset
         */
        resetFilter() {
            this.$emit('filter-reset');
        },

        /**
         * change page with index {index}
         *
         * @param {Number} index page number
         *
         * @emits Event#filter-update-current-page
         */
        changePage(index) {
            this.$emit('filter-update-current-page', index);
        }
    }
}
