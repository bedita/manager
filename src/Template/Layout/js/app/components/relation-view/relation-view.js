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
            method: 'relatedJson',                      // define AppController method to be used
            loading: false,
            count: 0,                                   // count number of related objects, on change triggers an event

            removedRelated: [],                         // staged removed related objects
            addedRelations: [],                         // staged added objects to be saved
            modifiedRelations: [],                      // staged modified relation params

            removedRelationsData: [],                   // hidden field containing serialized json passed on form submit
            addedRelationsData: [],                     // array of serialized new relations

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
                console.error('[relationToggle] needs first param (related) as {object} with property id set');
                return;
            }
            if (!this.containsId(this.removedRelated, related.id)) {
                this.removeRelation(related);
            } else {
                this.restoreRemovedRelation(related);
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
            this.prepareRelationsToRemove(this.removedRelated);
        },

        /**
         * re-add removed related object: removing it from removedRelated Array
         *
         * @param {Number} id
         * @param {String} type
         *
         * @returns {void}
         */
        restoreRemovedRelation(related) {
            let index = this.removedRelated.findIndex((rel) => rel.id !== related.id);
            this.removedRelated.splice(index, 1);
            this.prepareRelationsToRemove(this.removedRelated);
        },

        /**
         * format and serialize object relations
         *
         * @param {Array} relations
         *
         * @returns {void}
         */
        prepareRelationsToRemove(relations) {
            this.removedRelationsData = JSON.stringify(this.formatObjects(relations));
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
            if (!relations) {
                return;
            }
            this.removedRelated = relations;
            this.prepareRelationsToRemove(this.removedRelated);
        },

        /**
         * remove element with matched id from staged relations
         *
         * @param {Number} id
         *
         * @returns {void}
         */
        removeAddedRelations(id) {
            if (!id) {
                console.error('[removeAddedRelations] needs first param (id) as {Number|String}');
                return;
            }
            this.addedRelations = this.addedRelations.filter((rel) => rel.id !== id);
            this.prepareRelationsToSave();
        },

        /**
         * remove element with matched id from staged relations
         *
         * @param {Number} id
         *
         * @returns {void}
         */
        removeModifiedRelations(id) {
            if (!id) {
                console.error('[removeModifiedRelations] needs first param (id) as {Number|String}');
                return;
            }
            this.modifiedRelations = this.modifiedRelations.filter((rel) => rel.id !== id);
            this.prepareRelationsToSave();
        },

        /**
         * extract relation with modified params and set it to staging
         *
         * @param {Object} data
         *
         * @returns {void}
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

            this.modifyRelation(rel);
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
            this.prepareRelationsToSave();
        },

        /**
         * set modified relation to be saved
         *
         * @param {Object} related
         *
         * @returns {void}
         */
        modifyRelation(related) {
            if (this.containsId(this.modifiedRelations, related.id)) {
                // if object has been already modified we replace it within the modifiedRelations array
                this.modifiedRelations = this.modifiedRelations.map((object) => {
                    if (object.id === related.id) {
                        return related;
                    }
                    return object;
                });
            } else {
                // otherwise we add it to it
                this.modifiedRelations.push(related);
            }
            this.prepareRelationsToSave();
        },

        /**
         * set relations to be saved from both newly added and modified
         *
         * @returns {void}
         */
        prepareRelationsToSave() {
            const relations = this.addedRelations.concat(this.modifiedRelations);
            this.addedRelationsData = JSON.stringify(this.formatObjects(relations));
            this.$el.dispatchEvent(new Event('change', { bubbles: true }));
        },

        /**
         * frontend specific formatting for relation params
         *
         * @param {string} key
         * @param {any} value
         *
         * @returns {String} formatted value
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
         * helper function: build open view url
         *
         * @param {String} objectType
         * @param {Number} objectId
         *
         * @return {String} url
         */
        buildViewUrl(objectType, objectId) {
            return `${window.location.protocol}/${window.location.host}/${objectType}/view/${objectId}`;
        },
    }

}
