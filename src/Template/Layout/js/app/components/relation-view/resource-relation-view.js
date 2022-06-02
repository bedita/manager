/**
 *  Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *  Template/Elements/trees.twig
 *  Template/Elements/roles.twig
 *
 * <relation-view> component used for ModulesPage -> View
 *
 * @property {String} relationName name of the relation used by the PaginatiedContentMixin
 * @property {String} relationLabel label of the relation
 * @requires
 *
 */

import { PaginatedContentMixin } from 'app/mixins/paginated-content';

export default {
    mixins: [
        PaginatedContentMixin,
    ],

    props: {
        relationName: {
            type: String,
            required: true,
        },
        relationLabel: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            method: 'related',                      // define AppController method to be used
            loading: false,
            count: 0,                                   // count number of related objects, on change triggers an event
        }
    },

    /**
     * setup correct endpoint for PaginatedContentMixin.getPaginatedObjects()
     *
     * @return {void}
     */
    created() {
        this.endpoint = `${this.method}/${this.relationName}`;
    },

    /**
    * load content after component is mounted
    *
    * @return {void}
    */
    async mounted() {
        await this.loadOnMounted();
    },

    watch: {
        /**
         * Loading event emit
         *
         * @param {String} value The value associated to loading
         *
         * @emits Event#loading
         *
         * @return {void}
         */
        loading(value) {
            this.$emit('loading', value);
        },
    },

    methods: {
        /**
         * load content if flag set to true
         *
         * @return {void}
         */
        async loadOnMounted() {
            await this.loadRelatedObjects();
            return Promise.resolve();
        },

        /**
         * call PaginatedContentMixin.getPaginatedObjects() method and handle loading
         *
         * @emits Event#count count objects event
         *
         * @param {Object} filter object containing filters
         *
         * @return {Array} objs objects retrieved
         */
        async loadRelatedObjects(filter = {}) {
            this.loading = true;

            return this.getPaginatedObjects(true, filter)
                .then((objs) => {
                    this.$emit('count', this.pagination.count);
                    this.loading = false;
                    return objs;
                })
                .catch((error) => {
                    // code 20 is user aborted fetch which is ok
                    if (error.code !== 20) {
                        this.loading = false;
                        console.error(error);
                    }
                });
        },
    }

}
