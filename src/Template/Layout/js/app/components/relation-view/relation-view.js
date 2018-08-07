/**
 *  Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *  Template/Elements/trees.twig
 *  Template/Elements/trees.twig
 *
 * <relation-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName name of the relation used by the PaginatiedContentMixin
 * @prop {Boolean} loadOnStart load content on component init
 *
 */

import StaggeredList from 'app/components/staggered-list';
import RelationshipsView from 'app/components/relation-view/relationships-view/relationships-view';
import TreeView from 'app/components/tree-view/tree-view';
import sleep from 'sleep-promise';
import flatpickr from 'flatpickr/dist/flatpickr.min';

import { PaginatedContentMixin, DEFAULT_PAGINATION } from 'app/mixins/paginated-content';
import { RelationSchemaMixin } from 'app/mixins/relation-schema';

export default {
    // injected methods provided by Main App
    inject: ['requestPanel', 'closePanel'],
    mixins: [ PaginatedContentMixin, RelationSchemaMixin ],

    components: {
        StaggeredList,
        RelationshipsView,
        TreeView,
    },

    props: {
        relationName: {
            type: String,
            required: true,
        },
        loadOnStart: [Boolean, Number],
        multipleChoice: {
            type: Boolean,
            default: true,
        },
        configPaginateSizes: {
            type: String,
            default: '[]',
        },
    },

    data() {
        return {
            method: 'relatedJson',          // define AppController method to be used
            loading: false,
            count: 0,                       // count number of related objects, on change triggers an event

            removedRelated: [],             // currently related objects to be removed
            addedRelations: [],             // staged added objects to be saved
            modifiedRelations: [],
            relationsData: [],              // hidden field containing serialized json passed on form submit
            newRelationsData: [],           // array of serialized new relations

            pageSize: DEFAULT_PAGINATION.page_size,     // pageSize value for pagination page size
        }
    },

    computed: {
        // array of ids of objects in view
        alreadyInView() {
            var a = this.addedRelations.map(o => o.id);
            var b = this.objects.map(o => o.id);
            return a.concat(b);
        },
        paginateSizes() {
            return JSON.parse(this.configPaginateSizes);
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
     * load content if flag set to true after component is mounted
     *
     * @return {void}
     */
    mounted() {
        this.loadOnMounted();
    },

    watch: {
        /**
         * watcher for pageSize variable, change pageSize and reload relations
         *
         * @param {Number} value
         */
        pageSize(value) {
            this.setPageSize(value);
            this.loadRelatedObjects();
        },

        loading(value) {
            this.$emit('loading', value);
        },
    },

    methods: {
        async loadOnMounted() {
            if (this.loadOnStart) {
                var t = (typeof this.loadOnStart === 'number')? this.loadOnStart : 0;

                await sleep(t);
                if (this.relationSchema === null) {
                    await this.getRelationData();
                }

                await this.loadRelatedObjects();
            }
        },

        /**
         * call PaginatedContentMixin.getPaginatedObjects() method and handle loading
         *
         * @return {Boolean} response;
         */
        async loadRelatedObjects() {
            this.loading = true;

            let resp = await this.getPaginatedObjects();
            this.loading = false;
            this.$emit('count', this.pagination.count);
            return resp;
        },


        /**
         * toggle relation
         *
         * @param {object}
         *
         * @returns {void}
         */
        relationToggle(related) {
            if (!related || !related.id) {
                console.error('[reAddRelations] needs first param (related) as {object} with property id set');
                return;
            }
            if (!this.containsId(this.removedRelated, related.id)) {
                this.removeRelation(related);
            } else {
                this.undoRemoveRelation(related);
            }
        },


        /**
         * remove related object: adding it to removedRelated Array
         *
         * @param {String} type
         *
         * @returns {void}
         */
        removeRelation(related) {
            this.removedRelated.push(related);
            this.relationsData = JSON.stringify(this.removedRelated);
            this.$el.dispatchEvent(new Event('change', { bubbles: true }));
        },

        /**
         * re-add removed related object: removing it from removedRelated Array
         *
         * @param {Number} id
         * @param {String} type
         *
         * @returns {void}
         */
        undoRemoveRelation(related) {
            let index = this.removedRelated.findIndex((rel) => rel.id !== related.id);
            this.removedRelated.splice(index, 1);
            this.relationsData = JSON.stringify(this.removedRelated);
            this.$el.dispatchEvent(new Event('change', { bubbles: true }));
        },


        /**
         * prepare removeRelated Array for saving using serialized json input field
         *
         * @param {Array} relations
         *
         * @returns {void}
         */
        setRemovedRelated(relations) {
            if (!relations ) {
                return;
            }
            this.removedRelated = relations;
            this.relationsData = JSON.stringify(this.removedRelated);
        },


        /**
         * go to specific page
         *
         * @param {Number} page number
         *
         * @return {Promise} repsonse from server with new data
         */
        async toPage(i) {
            this.loading = true;
            let resp =  await PaginatedContentMixin.methods.toPage.call(this, i);
            this.loading = false;
            return resp;
        },


        /**
         * remove element with matched id from staged relations
         *
         * @param {Number} id
         */
        removeAddedRelations(id) {
            if (!id) {
                console.error('[removeAddedRelations] needs first param (id) as {Number|String}');
                return;
            }
            this.addedRelations = this.addedRelations.filter((rel) => rel.id !== id);
            this.setRelationsToSave();
        },


        /**
         * Event 'added-relations' callback
         * retrieve last added relations from relationships-view
         *
         * @param {Array} relations
         *
         * @return {void}
         */
        appendRelations(items) {
            if (!this.addedRelations.length) {
                this.addedRelations = items;
            } else {
                var existingIds = this.addedRelations.map(a => a.id);
                for (var i = 0; i < items.length; i++) {
                    if (existingIds.indexOf(items[i].id) < 0) {
                        this.addedRelations.push(items[i]);
                    }
                }
            }
            this.setRelationsToSave();
        },

        /**
         *
         *
         * @param {Object} data
         */
        updateRelationParams(data) {
            // id of edited related object
            const id = data.related.id;

            // extract related object from view
            const rel = this.objects.filter((object) => {
                if (object.id === id) {
                    return object;
                }
            }).pop();

            if (this.containsId(this.modifiedRelations, id)) {
                // if object has been already modified we replace it within the modifiedRelations array
                this.modifiedRelations = this.modifiedRelations.map((object) => {
                    if (object.id === id) {
                        return rel;
                    }
                    return object;
                });
            } else {
                // otherwise we add it to it
                this.modifiedRelations.push(rel);
            }
            this.setRelationsToSave();
        },

        /**
         * set relations to be saved from both newly added and modified
         *
         */
        setRelationsToSave() {
            const relations = this.addedRelations.concat(this.modifiedRelations);
            this.newRelationsData = JSON.stringify(relations);
            this.$el.dispatchEvent(new Event('change', { bubbles: true }));
        },

        /**
         * frontend specific formatting for relation params
         *
         * @param {string} key
         * @param {any} value
         */
        formatParam(key, value) {
            const schema = this.getRelationSchema();

            // formatting ISO 8061 date to human
            if (schema !== undefined && schema[key].format === 'date-time') {
                return flatpickr.formatDate(new Date(value), 'Y-m-d h:i K');
            }

            return value;
        },

        /**
         * helper function: check if array relations has element with id -> id
         *
         * @param {Array} relations
         * @param {Number} id
         *
         * @return {Boolean} true if id is in Array relations
         */
        containsId(relations, id) {
            return relations.filter((rel) => rel.id === id).length;
        },


        /**
         * helper function: build open view url
         *
         * @param {String} objectType
         * @param {Number} objectId
         *
         * @return {String} url
         */
        buildViewUrl(objectType, objectId) {
            return `${window.location.protocol}//${window.location.host}/${objectType}/view/${objectId}`;
        },
    }

}
