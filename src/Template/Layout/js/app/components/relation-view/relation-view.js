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
import FilterBoxView from 'app/components/filter-box';
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
        FilterBoxView,
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

            relationsData: [], // hidden field containing serialized json passed on form submit
            activeFilter: {}, // current active filter for objects list
        }
    },

    computed: {
        // array of ids of objects in view
        alreadyInView() {
            var a = this.addedRelations.map(o => o.id);
            var b = this.objects.map(o => o.id);
            return a.concat(b);
        },
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
    mounted() {
        this.loadOnMounted();
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
        // Events Listeners

        /**
         * listen to FilterBoxView event filter-objects
         *
         * @param {Object} filter
         *
         * @return {void}
         */
        onFilterObjects(filter) {
            this.activeFilter = filter;
            this.toPage(1, this.activeFilter);
        },

        /**
         * listen to FilterBoxView event filter-update-page-size
         *
         * @param {Number} pageSize
         *
         * @return {void}
         */
        onUpdatePageSize(pageSize) {
            this.setPageSize(pageSize);
            this.loadRelatedObjects(this.activeFilter);
        },

        /**
         * listen to FilterBoxView event filter-update-current-page
         *
         * @param {Number} page
         *
         * @return {void}
         */
        onUpdateCurrentPage(page) {
            this.toPage(page, this.activeFilter);
        },

        /**
         * load content if flag set to true
         *
         * @return {void}
         */
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
         * @emits Event#count count objects event
         *
         * @param {Object} filter object containing filters
         * @param {Boolean} force force recount of related objects
         *
         * @return {Array} objs objects retrieved
         */
        async loadRelatedObjects(filter = {}, force = false) {
            this.loading = true;
            let response = this.getPaginatedObjects(true, filter);

            response
                .then((objs) => {
                    this.$emit('count', this.pagination.count);
                    this.loading = false;
                    return objs;
                })
                .catch((error) => {
                    // code 20 is user aborted fetch which is ok
                    if (error.code !== 20) {
                        this.loading = false;
                        console.error(error);s
                    }
                });

            return response;
        },

        /**
         * reload all related objects
         *
         * @return {Array} objs objects retrieved
         */
        reloadObjects() {
            this.activeFilter = {};
            return this.loadRelatedObjects({}, true);
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

            // after relation is set to be removed we check if it was modified(therefore staged to be saved)
            if (this.containsId(this.modifiedRelations, related.id)) {
                // if yes we unstage it
                this.prepareRelationsToSave();
            }
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

            // after restore previously removed relation, we check if it was modified
            if (this.containsId(this.modifiedRelations, related.id)) {
                // if yes we stage it for saving
                this.prepareRelationsToSave();
            }
        },

        /**
         * format and serialize object relations
         *
         * @param {Array} relations list of related objects to format
         *
         * @returns {void}
         */
        prepareRelationsToRemove(relations) {
            this.removedRelationsData = JSON.stringify(this.formatObjects(relations));
            if (relations.length) {
                this.$el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        /**
         * prepare removeRelated Array for saving using serialized json input field
         *
         * @param {Array} relations list of related objects to be removed
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
         * @param {Array} relations list of related objects to add
         *
         * @return {void}
         */
        appendRelations(relations) {
            if (!this.addedRelations.length) {
                this.addedRelations = relations;
            } else {
                let existingIds = this.addedRelations.map(a => a.id);
                for (let i = 0; i < relations.length; i++) {
                    if (existingIds.indexOf(relations[i].id) < 0) {
                        this.addedRelations.push(relations[i]);
                    }
                }
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
            let difference = relations.filter(rel => !this.containsId(this.removedRelated, rel.id));

            this.addedRelationsData = JSON.stringify(this.formatObjects(difference));
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
         * go to specific page
         *
         * @param {Number} page The page number
         * @param {Object} filter The filter to apply
         *
         * @return {void}
         */
        toPage(page, filter) {
            this.loading = true;
            let response = PaginatedContentMixin.methods.toPage.call(this, page, filter);

            response
                .then((objs) => {
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
