/**
 * Filter Box View component
 *
 * allows to filter a list of objects with a text field, properties and a pagination toolbar
 *
 * <filter-box-view> component
 *
 * @prop {String} objectsLabel
 * @prop {String} placeholder
 * @prop {Boolean} showFilterButtons
 * @prop {Object} initFilter
 * @prop {Object} relationTypes relation types available for relation (left/right)
 * @prop {Array} filterList custom filters to show
 * @prop {Object} pagination
 * @prop {String} configPaginateSizes
 */

import { DEFAULT_PAGINATION, DEFAULT_FILTER } from 'app/mixins/paginated-content';
import merge from 'deepmerge';
import { t } from 'ttag';
import { warning } from 'app/components/dialog/dialog';

export default {
    components: {
        InputDynamicAttributes: () => import(/* webpackChunkName: "input-dynamic-attributes" */'app/components/input-dynamic-attributes'),
    },

    props: {
        objectsLabel: {
            type: String,
            default: t`Objects`
        },
        placeholder: {
            type: String,
            default: t`Search`
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
        selectedTypes: {
            type: Array,
            default: () => [],
        },
        filterList: {
            type: Array,
            default: () => [],
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
            queryFilter: {},
            timer: null,
            pageSize: this.pagination.page_size, // pageSize value for pagination page size
            dynamicFilters: {},
            statusFilter: {},
        };
    },

    created() {
        // merge default filters with initFilter
        let mergeFilters = this.formatFilters();
        this.queryFilter = merge.all([
            DEFAULT_FILTER,
            this.queryFilter,
            mergeFilters,
            this.initFilter
        ]);

        this.dynamicFilters = this.filterList.filter(f => {
            if(f.name == 'status') {
                this.statusFilter = f;
                return false;
            } else {
                return true;
            }
        });
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
        },

        selectedTypes(value) {
            this.queryFilter.filter.type = value;
        }
    },

    methods: {
        /**
         * trigger filter-objects event when query string has 3 or more carachter
         *
         * @emits Event#filter-objects
         */
        onQueryStringKeyup() {
            const queryString = this.queryFilter.q || "";

            clearTimeout(this.timer);
            if (queryString.trim().length >= 3 || queryString.trim().length === 0) {
                this.timer = setTimeout(() => {
                    this.$emit("filter-objects", this.queryFilter);
                }, 300);
            }
        },

        /**
         * Handle Search input when value changes
         * If search text is empty or at least 3 characters long, ok.
         * Otherwise, warn that search text is too short.
         */
        onQueryStringChange() {
            if (!this.searchFieldValid()) {
                this.searchFieldDialog();
            }
        },

        /**
         * Verify search text.
         * If is empty or more than 2 characters long, return true.
         * Return false otherwise.
         */
        searchFieldValid() {
            this.queryFilter.q = this.queryFilter.q.trim();

            return (this.queryFilter.q.length === 0 || this.queryFilter.q.length > 2);
        },

        /**
         * Verify search text.
         * If is empty or more than 2 characters long, ok.
         * Show prompt dialog otherwise.
         */
        searchFieldDialog() {
            warning(t`Search text too short. Minimum length is 3. Retry`);
        },

        onOtherFiltersChange() {
            this.$emit("filter-objects", this.queryFilter);
        },

        /**
         * load custom filters property names
         *
         * @returns {Object} filters' name
         */
        formatFilters() {
            let filter = {};
            this.filterList.forEach (
                f => (filter[f.name] = f.date ? {} : "")
            );

            return { filter: filter };
        },

        /**
         * apply filters
         *
         * @emits Event#filter-objects-submit
         */
        applyFilter() {
            if (this.searchFieldValid()) {
                this.$emit("filter-objects-submit", this.queryFilter);

                return;
            }
            this.searchFieldDialog();
        },

        /**
         * reset filters
         *
         * @emits Event#filter-reset
         */
        resetFilter() {
            this.$emit("filter-reset");
        },

        /**
         * change page with index {index}
         *
         * @param {Number} index page number
         *
         * @emits Event#filter-update-current-page
         */
        onChangePage(index) {
            this.$emit("filter-update-current-page", index);
        }
    }
};
