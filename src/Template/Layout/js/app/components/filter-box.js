/**
 * Filter Box View component
 *
 * allows to filter a list of objects with a text field, properties and a pagination toolbar
 *
 * <filter-box-view> component
 *
 * @prop {String} configPaginateSizes
 * @prop {Boolean} filterActive Some filter is active on currently displayed data
 * @prop {Array} filterList custom filters to show
 * @prop {Array} filtersByType Map of available filters grouped by object type
 * @prop {Object} initFilter Initial filter
 * @prop {String} objectsLabel
 * @prop {Object} pagination
 * @prop {String} placeholder
 * @prop {Object} relationTypes relation types available for relation (left/right)
 * @prop {Boolean} showAdvanced Flag to enable advanced filters. default: true
 */

import { DEFAULT_PAGINATION, getDefaultFilter } from 'app/mixins/paginated-content';
import merge from 'deepmerge';
import { t } from 'ttag';
import { warning } from 'app/components/dialog/dialog';

export default {
    components: {
        InputDynamicAttributes: () => import(/* webpackChunkName: "input-dynamic-attributes" */'app/components/input-dynamic-attributes'),
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */'app/components/category-picker/category-picker'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
    },

    props: {
        configPaginateSizes: {
            type: String,
            default: "[10]"
        },
        filterActive: Boolean,
        filterList: {
            type: Array,
            default: () => [],
        },
        filtersByType: {
            type: Object,
        },
        initFilter: {
            type: Object,
            default: getDefaultFilter,
        },
        objectsLabel: {
            type: String,
            default: t`Objects`
        },
        pagination: {
            type: Object,
            default: () => DEFAULT_PAGINATION
        },
        placeholder: {
            type: String,
            default: t`Search`
        },
        relationTypes: {
            type: Object
        },
        showAdvanced: {
            type: Boolean,
            default: true
        },
    },

    data() {
        return {
            availableFilters: [],
            dynamicFilters: [],
            /**
             * Enable position filter by descendants.
             * When disabled, only direct children are fetched.
             * This will switch the API filter between `parent` and `ancestor`.
             */
            filterByDescendants: false,
            moreFilters: this.filterActive,
            pageSize: this.pagination.page_size,
            queryFilter: {},
            selectedStatuses: [],
            selectedType: '',
            statusFilter: {},
            timer: null,
        };
    },

    created() {
        this.queryFilter = merge.all([
            this.getCleanQuery(this.filterList),
            this.initFilter,
        ]);

        if (this.filterList.length) {
            this.availableFilters = this.filterList;
        } else if (this.rightTypes.length == 1 && this.filtersByType) {
            this.availableFilters = this.filtersByType[this.rightTypes[0]];
        }
        this.selectedStatuses = Object.values(this.initFilter?.filter?.status || {});
        this.filterByDescendants = !!this.initFilter?.filter?.ancestor;
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
            return this.relationTypes?.right || [];
        },

        canShowAdvanced() {
            return (this.availableFilters?.length && this.showAdvanced) || this.rightTypes.length > 1;
        },

        /**
         * Check if the value of the search input is valid to perform a search.
         * It has to be empty or longer than 2 characters.
         *
         * @returns {boolean}
         */
        isSearchFieldValid() {
            const length = this.queryFilter?.q?.length;
            return length === 0 || length > 2;
        },

        /**
         * check which navigation layout needs to be rendered
         *
         * @return {void}
         */
        isFullPaginationLayout() {
            return this.pagination.page_count > 1 && this.pagination.page_count <= 7;
        },

        initHistoryEditor() {
            this.queryFilter.filter['history_editor'] = Boolean(this.initFilter?.filter?.history_editor);

            return Boolean(this.initFilter?.filter?.history_editor) || false;
        },

        initCategories() {
            const categories = [];
            let filterCategories = this.initFilter?.filter?.categories || '';
            if (filterCategories.length === 0) {
                return categories;
            }
            filterCategories = filterCategories.split(',');
            filterCategories.forEach(item => {
                categories.push({name: item});
            });

            return categories;
        },

        initTags() {
            const tags = [];
            let filterTags = this.initFilter?.filter?.tags || '';
            if (filterTags.length === 0) {
                return tags;
            }
            filterTags = filterTags.split(',');
            filterTags.forEach(item => {
                tags.push({name: item, label: item});
            });

            return tags;
        },

        initFolder() {
            if (!this.initFilter?.filter) {
                return '';
            }

            return this.initFilter?.filter[this.positionFilterName] || '';
        },

        /**
         * Capitalize `objectsLabel`.
         * Fallback to the translation of "Items".
         *
         * @returns {String}
         */
        label() {
            if (!this.objectsLabel) {
                return t`Items`;
            }

            return this.objectsLabel.charAt(0) + this.objectsLabel.slice(1);
        },

        positionFilterName() {
            return this.filterByDescendants ? 'ancestor' : 'parent';
        }
    },

    watch: {
        /**
         * Normalize query filter and remove custom filters from the list of dynamic filters when `availableFilters` is updated.
         */
        availableFilters() {
            this.normalizeQueryFilter();
            this.dynamicFilters = this.availableFilters.filter(f => {
                if (f.name == 'status') {
                    this.statusFilter = f;

                    return false;
                }

                return true;
            });
        },

        /**
         * watcher for pageSize variable, change pageSize and reload relations
         *
         * @emits Event#filter-update-page-size
         *
         * @returns {void}
         */
        pageSize() {
            this.$emit("filter-update-page-size", this.pageSize);
        },

        /**
         * Add selected statuses to the query filters.
         * @param {String[]} value Selected statuses list
         */
        selectedStatuses(value) {
            this.queryFilter.filter.status = value;
        },

        /**
         * Process dynamic filters list when selected object type changes.
         * Then apply the current filter.
         * @param {String} type Selected object type
         */
        selectedType(type) {
            this.availableFilters = [];
            if (this.filtersByType && this.filtersByType[type]) {
                this.availableFilters = this.filtersByType[type];
            };
            const query = this.getCleanQuery();
            // persist old compatible filter values
            query.q = this.queryFilter.q;
            Object.keys(query.filter).forEach(f => {
                const oldFilter = this.queryFilter.filter[f];
                if (oldFilter) {
                    query.filter[f] = oldFilter;
                }
            });
            query.filter.type = type;
            this.queryFilter = query;
            this.applyFilter();
        }
    },

    methods: {
        /**
         * Build clean query filter object initializing all filters from `availableFilters`.
         *
         * @returns {Object} clean query object
         */
        getCleanQuery() {
            const query = getDefaultFilter();
            this.availableFilters.forEach(f => {
                const defaultValue = f.date ? {} : '';
                query.filter[f.name] = defaultValue;
            });

            return query;
        },

        /**
         * Normalize query filter object initializing all filters from `availableFilters` and persisting already set values.
         */
        normalizeQueryFilter() {
            const filterObj = this.getCleanQuery().filter;
            this.availableFilters.forEach(f => {
                const defaultValue = f.date ? {} : '';
                const currentValue = this.queryFilter.filter[f.name];
                filterObj[f.name] = currentValue || defaultValue;
            });
            this.queryFilter.filter = filterObj;
        },

        /**
         * Check if filter value is empty.
         * @param {*} filterVal Value to check
         * @returns {Boolean}
         */
        isFilterValueEmpty(filterVal) {
            if (typeof filterVal === 'object') {
                return Object.values(filterVal).every((prop) => !prop);
            }

            if (Array.isArray(filterVal)) {
                return !filterVal.length;
            }

            return !filterVal;
        },

        /**
         * Prepare filter object to perform a filter action.
         * Clean the filter object removing empty properties and
         * filters not available for the current object type.
         *
         * @returns {Object} filter object ready for the search
         */
        prepareFilters() {
            const filter = { ...this.queryFilter.filter };

            Object.entries(filter).forEach(([key, filterValue]) => {
                // do nothing allowed filters, if value is set
                if (['status', 'type', 'ancestor', 'history_editor'].includes(key) && filterValue) {
                    return;
                }

                // remove the filter if it doesn't appear in the list of available filters
                if (!this.dynamicFilters.find(f => f.name == key)) {
                    delete filter[key];
                    return;
                }

                // reset the filter if it's an object or array and it only contains empty properties
                if (this.isFilterValueEmpty(filterValue)) {
                    delete filter[key];
                }
            });

            return filter;
        },

        /**
         * apply filters
         *
         * @emits Event#filter-objects
         */
        applyFilter() {
            if (!this.isSearchFieldValid) {
                return warning(t`Search text too short. Minimum length is 3. Retry`);
            }

            const filter = this.prepareFilters();
            this.$emit("filter-objects", { ...this.queryFilter, filter });
        },

        /**
         * Reset filters
         *
         * @emits Event#filter-reset
         */
        resetFilter() {
            this.selectedStatuses = [];
            this.selectedType = '';
            this.queryFilter = this.getCleanQuery();
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
        },
        onChangePageNumber(e) {
            let val = e.target.value;
            val = val.trim();
            if (!val) {
                return;
            }
            val = parseFloat(val);
            if (!val || val > this.pagination.page_count) {
                e.target.value = '';

                return;
            }
            this.onChangePage(val);
        },
        onPageKeydown(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                this.onChangePageNumber(e);

                return false;
            }
        },
        onCategoryChange(categories) {
            this.queryFilter.filter.categories = categories?.map((cat) => cat.name).join(',');
        },

        onTagChange(tags) {
            this.queryFilter.filter.tags = tags?.map((tag) => tag.id).join(',');
        },

        onFolderChange(folder) {
            this.queryFilter.filter[this.positionFilterName] = folder?.id;
        },

        /**
         * Switch between descendants and children filter.
         * @param {Event} event Change event of the switch input.
         */
        onPositionFilterChange(event) {
            const value = event.target.checked;
            const newFilter = value ? 'ancestor' : 'parent';
            const oldFilter = value ? 'parent' : 'ancestor';

            this.queryFilter.filter[newFilter] = this.queryFilter.filter[oldFilter];
            delete this.queryFilter.filter[oldFilter];
        },
    }
};
