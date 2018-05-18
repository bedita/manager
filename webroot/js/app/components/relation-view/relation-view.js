/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *  Template/Elements/trees.twig
 *
 * <relation-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName name of the relation used by the PaginatiedContentMixin
 * @prop {Boolean} loadOnStart load content on component init
 *
 */

Vue.component('relation-view', {
    mixins: [ PaginatedContentMixin ],

    // defining props with validation
    props: {
        relationName: {
            type: String,
            required: true,
        },
        loadOnStart: {
            type: Boolean,
            default: false,
        },
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
            showRelationshipsPanel: false,
            count: 0,                       // count number of related objects, on change triggers an event

            removedRelated: [],             // currently related objects to be removed
            addedRelations: [],             // staged added objects to be saved
            hideRelations: [],              // hide already added relations in add-relations-view
            relationsData: [],              // hidden field containing serialized json passed on form submit
            newRelationsData: [],           // array of serialized new relations

            step: DEFAULT_PAGINATION.page_size,     // step value for pagination page size
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
        if (this.loadOnStart) {
            this.loadRelatedObjects();
        }
    },

    watch: {
        /**
         * watcher for step variable, change pageSize and reload relations
         *
         * @param {Number} value
         */
        step(value) {
            this.setPageSize(value);
            this.loadRelatedObjects();
        },

        loading(value) {
            this.$emit('loading', value);
        },
    },

    methods: {
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
         * @param {Number} id
         * @param {String} type
         *
         * @returns {void}
         */
        removeRelation(related) {
            this.removedRelated.push(related);
            this.relationsData = JSON.stringify(this.removedRelated);
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
            this.removedRelated = this.removedRelated.filter((rel) => rel.id !== related.id);
            this.relationsData = JSON.stringify(this.removedRelated);
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
            this.relationsData = this.relationFormatterHelper(this.removedRelated);
        },

        /**
         * show next page of paginated content
         *
         */
        async showMoreRelated() {
            this.loading = true;
            await this.loadMore(this.step);
            this.loading = false;
        },

        /**
         * fo to specific page
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
         * load first page of content returns newly loaded objects
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        async firstPage(autoload = true) {
            this.loading = true;

            // calling Mixin's method
            let resp =  await PaginatedContentMixin.methods.firstPage.call(this, autoload);
            this.loading = false;

            return resp;
        },

        /**
         * load last page of content returns newly loaded objects
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        async lastPage(autoload = true) {
            this.loading = true;

            // calling Mixin's method
            let resp =  await PaginatedContentMixin.methods.lastPage.call(this, autoload);
            this.loading = false;

            return resp;
        },

        /**
         * load next page of content returns newly loaded objects
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        async nextPage(autoload = true) {
            this.loading = true;

            // calling Mixin's method
            let resp =  await PaginatedContentMixin.methods.nextPage.call(this, autoload);
            this.loading = false;

            return resp;
        },

        /**
         * load prev page of content returns newly loaded objects
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        async prevPage(autoload = true) {
            this.loading = true;

            // calling Mixin's method
            let resp =  await PaginatedContentMixin.methods.prevPage.call(this, autoload);
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
        },

        /**
         * Show relationships-view and pass objects already related which need to be hidden
         *
         * @return {void}
         */
        showRelationshipsModal() {
            // this.hideRelations is passed as prop to relationships-view
            this.hideRelations = this.objects;
            this.showRelationshipsPanel = true;
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
            this.newRelationsData = JSON.stringify(this.addedRelations);
        },

        /**
         * Event 'visibility-setter' callback
         * set current view's relationships-view visibility from child view
         *
         * @param {Boolean} isVisible
         *
         * @return {void}
         */
        setRelationshipPanelVisibility(isVisible) {
            this.showRelationshipsPanel = isVisible;
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
         * helper function: convert array to string
         *
         * @param {Array} relations
         *
         * @return {String} string version of relations
         */
        relationFormatterHelper(relations) {
            let jsonString = '';
            try {
                jsonString = JSON.stringify(relations);
            } catch(err) {
                console.error(err);
            }
            return jsonString;
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










        requestPanel() {
            // emit event in module view
            this.$parent.$parent.$emit('request-panel', {
                relation: {
                    name: this.relationName,
                    alreadyInView: this.alreadyInView,
                },
            });
        }
    }

});
