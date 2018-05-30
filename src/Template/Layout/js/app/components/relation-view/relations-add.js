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
        }
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

        loading(value) {
            this.$emit('loading', value);
        },
    },

    methods: {
        returnData() {
            var data = {
                objects: this.selectedObjects,
                relationName: this.relationName,
            };
            this.$root.onRequestPanelToggle({ returnData: data });
        },
        toggle(object, evt) {
            let position = this.selectedObjects.indexOf(object);
            if(position != -1) {
                this.selectedObjects.splice(position, 1);
            } else {
                this.selectedObjects.push(object);
            }
        },
        isAlreadyRelated() {
            return true;
        },
        // form mixin
        async loadObjects() {
            console.log('LOAD OBJECTS');
            this.loading = true;
            let resp = await this.getPaginatedObjects();
            this.loading = false;
            this.$emit('count', this.pagination.count);
            return resp;
        },

        /**
         * go to specific page
         *
         * @param {Number} page number
         *
         * @return {Promise} repsonse from server with new data
         */
        async toPage(i) {
            console.log('TO PAGE');
            this.loading = true;
            let resp =  await PaginatedContentMixin.methods.toPage.call(this, i);
            this.loading = false;
            return resp;
        },
    }

}
