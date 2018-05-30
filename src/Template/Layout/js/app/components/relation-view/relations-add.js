/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relations-add> component used for Panel
 *
 */

import { PaginatedContentMixin, DEFAULT_PAGINATION } from 'app/mixins/paginated-content';
import decamelize from 'decamelize';

export default {
    mixins: [ PaginatedContentMixin ],
    props: {
        relationName: {
            type: String,
            default: '',
        },
        alreadyInView: {
            type: Array,
            default: () => [],
        },
        configPaginateSizes: {
            type: String,
            default: '[]',
        },
    },
    data() {
        return {
            method: 'relationshipsJson',
            endpoint: '',
            selectedObjects: [],
            pageSize: DEFAULT_PAGINATION.page_size,
            filter: '',
            queryFilter: {},
        };
    },

    computed: {
        relationHumanizedName() {
            return decamelize(this.relationName);
        },
        paginateSizes() {
            return JSON.parse(this.configPaginateSizes);
        }
    },

    watch: {
        relationName: {
            immediate: true,
            handler(newVal, oldVal) {
                if (newVal) {
                    this.selectedObjects = [];
                    this.endpoint = `${this.method}/${newVal}`;
                    this.loadObjects();
                }
            },
        },
        /**
         * watcher for pageSize variable, change pageSize and reload relations
         *
         * @param {Number} value
         */
        pageSize(value) {
            this.setPageSize(value);
            this.loadObjects();
        },

        /**
         * Loading event emit
         *
         * @param {String} value The value associated to loading
         * @return {void}
         */
        loading(value) {
            this.$emit('loading', value);
        },

        /**
         * watcher for text filter
         * if value is more than 3 chars, trigger api call to search by filter
         *
         * @param {String} value The filter string
         * @return {void}
         */
        filter(value) {
            this.filter = value;
            if (this.filter.length >= 3 || this.filter.length == 0) {
                this.queryFilter = {
                    q: this.filter
                };
                this.loadObjects();
            }
        }
    },

    methods: {
        /**
         * Load data for panel.
         *
         * @return {void}
         */
        returnData() {
            var data = {
                objects: this.selectedObjects,
                relationName: this.relationName,
            };
            this.$root.onRequestPanelToggle({ returnData: data });
        },

        /**
         * Add/remove elements to selectedObjects list
         *
         * @param {Object} object The object
         * @param {Event} evt The event
         * @return {void}
         */
        toggle(object, evt) {
            let position = this.selectedObjects.indexOf(object);
            if(position != -1) {
                this.selectedObjects.splice(position, 1);
            } else {
                this.selectedObjects.push(object);
            }
        },

        /**
         * Load objects (using filter and pagination)
         *
         * @return {Promise} repsonse from server
         */
        async loadObjects() {
            this.loading = true;
            let response = await this.getPaginatedObjects(true, this.queryFilter);
            this.loading = false;
            this.$emit('count', this.pagination.count);

            return response;
        },

        /**
         * Go to specific page
         *
         * @param {Number} page The page number
         * @return {Promise} The response from server with new data
         */
        async toPage(i) {
            this.loading = true;
            let resp =  await PaginatedContentMixin.methods.toPage.call(this, i);
            this.loading = false;
            return resp;
        },
    }

}
