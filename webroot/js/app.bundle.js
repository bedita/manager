(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["app"],{

/***/ "./src/Template/Layout/js/app/app.js":
/*!*******************************************!*\
  !*** ./src/Template/Layout/js/app/app.js ***!
  \*******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue/dist/vue.min.js */ "./node_modules/vue/dist/vue.min.js");
/* harmony import */ var vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var Template_Layout_style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! Template/Layout/style.scss */ "./src/Template/Layout/style.scss");
/* harmony import */ var Template_Layout_style_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(Template_Layout_style_scss__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var app_pages_modules_index__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! app/pages/modules/index */ "./src/Template/Layout/js/app/pages/modules/index.js");
/* harmony import */ var app_pages_modules_view__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! app/pages/modules/view */ "./src/Template/Layout/js/app/pages/modules/view.js");
/* harmony import */ var app_components_relation_view_relations_add__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! app/components/relation-view/relations-add */ "./src/Template/Layout/js/app/components/relation-view/relations-add.js");
/* harmony import */ var app_directives_datepicker__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! app/directives/datepicker */ "./src/Template/Layout/js/app/directives/datepicker.js");
/* harmony import */ var app_directives_jsoneditor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! app/directives/jsoneditor */ "./src/Template/Layout/js/app/directives/jsoneditor.js");
/* harmony import */ var app_directives_richeditor__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! app/directives/richeditor */ "./src/Template/Layout/js/app/directives/richeditor.js");
/* harmony import */ var v_hotkey__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! v-hotkey */ "./node_modules/v-hotkey/index.js");
/* harmony import */ var v_hotkey__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(v_hotkey__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var config_config__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! config/config */ "./src/Template/Layout/js/config/config.js");















vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a.use(app_directives_jsoneditor__WEBPACK_IMPORTED_MODULE_6__["default"]);
vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a.use(app_directives_datepicker__WEBPACK_IMPORTED_MODULE_5__["default"]);
vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a.use(app_directives_richeditor__WEBPACK_IMPORTED_MODULE_7__["default"]);
vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a.use(v_hotkey__WEBPACK_IMPORTED_MODULE_8___default.a);



// merge vue options, config from configuration file
for (let property in config_config__WEBPACK_IMPORTED_MODULE_9__["VueConfig"]) {
    if (config_config__WEBPACK_IMPORTED_MODULE_9__["VueConfig"].hasOwnProperty(property)) {
        vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a.config[property] = config_config__WEBPACK_IMPORTED_MODULE_9__["VueConfig"][property];
    }
}

for (let property in config_config__WEBPACK_IMPORTED_MODULE_9__["VueOptions"]) {
    if (config_config__WEBPACK_IMPORTED_MODULE_9__["VueOptions"].hasOwnProperty(property)) {
        vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a.options[property] = config_config__WEBPACK_IMPORTED_MODULE_9__["VueOptions"][property];
    }
}

const _vueInstance = new vue_dist_vue_min_js__WEBPACK_IMPORTED_MODULE_0___default.a({
    el: 'main',

    components: {
        ModulesIndex: app_pages_modules_index__WEBPACK_IMPORTED_MODULE_2__["default"],
        ModulesView: app_pages_modules_view__WEBPACK_IMPORTED_MODULE_3__["default"],
        TrashIndex: app_pages_modules_index__WEBPACK_IMPORTED_MODULE_2__["default"],
        TrashView: app_pages_modules_view__WEBPACK_IMPORTED_MODULE_3__["default"],
        RelationsAdd: app_components_relation_view_relations_add__WEBPACK_IMPORTED_MODULE_4__["default"],
    },

    data() {
        return {
            vueLoaded: false,
            urlPagination: '',
            searchQuery: '',
            pageSize: '100',
            page: '',
            sort: '',
            panelIsOpen: false,
            addRelation: {},
        }
    },

    created() {
        this.vueLoaded = true;

        // load url params when component initialized
        this.loadUrlParams();
    },

    methods: {
        // panel
        onRequestPanelToggle(evt) {
            this.panelIsOpen = !this.panelIsOpen;
            var cl = document.querySelector('html').classList;
            cl.contains('is-clipped')? cl.remove('is-clipped') : cl.add('is-clipped');

            // return data from panel
            if(evt.returnData) {
                if(evt.returnData.relationName){
                    this.$refs["moduleView"]
                        .$refs[evt.returnData.relationName]
                        .$refs["relation"].appendRelations(evt.returnData.objects);
                }
            }

            // open panel for relations add
            if(this.panelIsOpen && evt.relation && evt.relation.name) {
                this.addRelation = evt.relation;
            } else {
                sleep(500).then(() => this.addRelation = {}); // 500ms is the panel transition duration
            }
        },


        /**
         * extract params from page url
         *
         * @returns {void}
         */
        loadUrlParams() {
            // look for query string params in window url
            if (window.location.search) {
                const urlParams = window.location.search;

                // search for q='some string' both after ? and & tokens
                const queryStringExp = /[?&]q=([^&#]*)/g;
                let matches = urlParams.match(queryStringExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(queryStringExp, '$1'));
                    this.searchQuery = matches[0];
                }

                // search for page_size='some string' both after ? and & tokens
                const pageSizeExp = /[?&]page_size=([^&#]*)/g;
                matches = urlParams.match(pageSizeExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(pageSizeExp, '$1'));
                    this.pageSize = this.isNumeric(matches[0]) ? matches[0] : '';
                }

                // search for page='some string' both after ? and & tokens
                const pageExp = /[?&]page=([^&#]*)/g;
                matches = urlParams.match(pageExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(pageExp, '$1'));
                    this.page = this.isNumeric(matches[0]) ? matches[0] : '';
                }

                // search for sort='some string' both after ? and & tokens
                const sortExp = /[?&]sort=([^&#]*)/g;
                matches = urlParams.match(sortExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(sortExp, '$1'));
                    this.sort = matches[0];
                }
            }
        },

        /**
         * build coherent url based on these params:
         * - q= query string
         * - page_size
         *
         * @param {Object} params
         * @returns {String} url
         */
        buildUrlParams(params) {
            let url = `${window.location.origin}${window.location.pathname}`;
            let first = true;

            Object.keys(params).forEach((key) =>  {
                if (params[key] && params[key] !== '') {
                    url += `${first ? '?' : '&'}${key}=${params[key]}`;
                    first = false;
                }
            });

            return url;
        },


        /**
         * update pagination keeping searched string
         *
         * @returns {void}
         */
        updatePagination() {
            window.location.replace(this.urlPagination);
        },

        /**
         * search queryString keeping pagination options
         *
         * @returns {void}
         */
        search() {
            this.page = '';
            this.applyFilters();
        },


        /**
         * reset queryString in search keeping pagination options
         *
         * @returns {void}
         */
        resetResearch() {
            this.searchQuery = '';
            this.applyFilters();
        },

        /**
         * apply page filters such as query string or pagination
         *
         * @returns {void}
         */
        applyFilters() {
            let url = this.buildUrlParams({
                q: this.searchQuery,
                page_size: this.pageSize,
                page: this.page,
                sort: this.sort,
            });
            window.location.replace(url);
        },

        /**
         * Helper function
         *
         * @param {String|Number} num
         * @returns {Boolean}
         */
        isNumeric(num) {
            return !isNaN(num);
        },
    }
});

window._vueInstance = _vueInstance;


/***/ }),

/***/ "./src/Template/Layout/js/app/components/property-view/property-view.js":
/*!******************************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/property-view/property-view.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_components_relation_view_relation_view__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/components/relation-view/relation-view */ "./src/Template/Layout/js/app/components/relation-view/relation-view.js");
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/Form/other_properties.twig
 *  Template/Elements/Form/core_properties.twig
 *  Template/Elements/Form/meta.twig
 *
 * <property-view> component used for ModulesPage -> View
 *
 * Component that wraps group of properties in the object View
 *
 * @prop {Boolean} tabOpen determines whether the property content is visible or not
 * @prop {String} label label of the property view
 *
 */



/* harmony default export */ __webpack_exports__["default"] = ({
    components: {
        RelationView: app_components_relation_view_relation_view__WEBPACK_IMPORTED_MODULE_0__["default"],
    },

    props: {
        tabOpen: {
            type: Boolean,
            default: true,
        },
        isDefaultOpen: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            isOpen: true,
            isLoading: false,
            count: 0,
        }
    },

    mounted() {
        this.isOpen = this.isDefaultOpen;
    },

    watch: {
        tabOpen() {
            this.isOpen = this.tabOpen;
        },
    },

    methods: {
        toggleVisibility() {
            this.isOpen = !this.isOpen;
        },
        onToggleLoading(status) {
            this.isLoading = status;
        },
        onCount(n) {
            this.count = n;
        },
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/relation-view/relation-view.js":
/*!******************************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/relation-view/relation-view.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_components_staggered_list__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/components/staggered-list */ "./src/Template/Layout/js/app/components/staggered-list.js");
/* harmony import */ var app_components_relation_view_relationships_view_relationships_view__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/components/relation-view/relationships-view/relationships-view */ "./src/Template/Layout/js/app/components/relation-view/relationships-view/relationships-view.js");
/* harmony import */ var app_components_tree_view_tree_view__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! app/components/tree-view/tree-view */ "./src/Template/Layout/js/app/components/tree-view/tree-view.js");
/* harmony import */ var sleep_promise__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! sleep-promise */ "./node_modules/sleep-promise/build/esm.mjs");
/* harmony import */ var app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! app/mixins/paginated-content */ "./src/Template/Layout/js/app/mixins/paginated-content.js");
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








/* harmony default export */ __webpack_exports__["default"] = ({
    mixins: [ app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_4__["PaginatedContentMixin"] ],
    components: {
        StaggeredList: app_components_staggered_list__WEBPACK_IMPORTED_MODULE_0__["default"],
        RelationshipsView: app_components_relation_view_relationships_view_relationships_view__WEBPACK_IMPORTED_MODULE_1__["default"],
        TreeView: app_components_tree_view_tree_view__WEBPACK_IMPORTED_MODULE_2__["default"],
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
            relationsData: [],              // hidden field containing serialized json passed on form submit
            newRelationsData: [],           // array of serialized new relations

            pageSize: app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_4__["DEFAULT_PAGINATION"].page_size,     // pageSize value for pagination page size
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
                await Object(sleep_promise__WEBPACK_IMPORTED_MODULE_3__["default"])(t);
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
            let resp =  await app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_4__["PaginatedContentMixin"].methods.toPage.call(this, i);
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


        /**
         * request panel emitting event in module view
         *
         * @param {String} objectType
         * @param {Number} objectId
         *
         * @return {String} url
         */
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


/***/ }),

/***/ "./src/Template/Layout/js/app/components/relation-view/relations-add.js":
/*!******************************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/relation-view/relations-add.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/mixins/paginated-content */ "./src/Template/Layout/js/app/mixins/paginated-content.js");
/* harmony import */ var decamelize__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! decamelize */ "./node_modules/decamelize/index.js");
/* harmony import */ var decamelize__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(decamelize__WEBPACK_IMPORTED_MODULE_1__);
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relations-add> component used for Panel
 *
 */




/* harmony default export */ __webpack_exports__["default"] = ({
    mixins: [ app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_0__["PaginatedContentMixin"] ],
    props: {
        relationName: {
            type: String,
            default: '',
        },
        alreadyInView: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            method: 'relationshipsJson',
            endpoint: '',
            selectedObjects: [],
        }
    },

    computed: {
        relationHumanizedName() {
            return decamelize__WEBPACK_IMPORTED_MODULE_1___default()(this.relationName);
        }
    },

    watch: {
        relationName: {
            immediate: true,
            handler(newVal, oldVal) {
                if(newVal) {
                    this.selectedObjects = [];
                    this.endpoint = `${this.method}/${newVal}`;
                    this.loadObjects();
                }
            },
        }
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
            this.loading = true;
            let resp = await this.getPaginatedObjects();
            this.loading = false;
            return resp;
        },
    }

});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/relation-view/relationships-view/relationships-view.js":
/*!******************************************************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/relation-view/relationships-view/relationships-view.js ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/mixins/paginated-content */ "./src/Template/Layout/js/app/mixins/paginated-content.js");
/* harmony import */ var app_components_staggered_list__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/components/staggered-list */ "./src/Template/Layout/js/app/components/staggered-list.js");
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relationships-view> component used for ModulesPage -> View
 *
 */




/* harmony default export */ __webpack_exports__["default"] = ({
    mixins: [ app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_0__["PaginatedContentMixin"] ],
    components: {
        StaggeredList: app_components_staggered_list__WEBPACK_IMPORTED_MODULE_1__["default"],
    },

    // props used by parent
    props: {
        relationName: {
            type: String,
            required: true,
        },
        viewVisibility: {
            type: Boolean,
            default: () => false,
        },
        addedRelations: {
            type: Array,
            default: () => [],
        },
        // relations already related to the object
        hideRelations: {
            type: Array,
            default: () => [],
        }
    },

    computed: {
        keyEvents() {
            return {
                'esc': {
                    keyup: this.handleKeyboard,
                }
            };
        },
    },

    data() {
        return {
            method: 'relationshipsJson',    // define AppController method to be used
            loading: false,
            pendingRelations: [],           // pending elements to be added
            relationsData: [],              // hidden field containing serialized json passed on form submit
            isVisible: false,
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

    watch: {
        /**
         * watch elements staged from relation-view (view can delete last added elements before saving)
         *
         * @param {Array} relations
         *
         * @return {void}
         */
        addedRelations(relations) {
            this.pendingRelations = relations;
        },

        /**
         * watch pendingRelations Array and prepare it for saving using serialized json input field
         *
         * @param {Array} relations
         *
         * @return {void}
         */
        pendingRelations(relations) {
            this.relationsData = this.relationFormatterHelper(relations);
        },

        /**
         * set isVisible value from property's viewVisibility change
         *
         * @param {Boolean} value
         *
         * @return {void}
         */
        viewVisibility(value) {
            this.isVisible = value;
        },

        /**
         * emit event to parent
         * load data when empty
         * @event 'visibility-setter'
         *
         * @return {void}
         */
        isVisible() {
            if (!this.objects.length) {
                this.loadObjects();
            }
            // avoid problem with vue rendering queue
            this.$nextTick( () => {
                if (this.isVisible && this.$refs.inputFilter) {
                    this.$refs.inputFilter.focus();
                }
            });

            // emit event to pass data to parent
            this.$emit('visibility-setter', this.isVisible);
        },

        loading(value) {
            this.$parent.$emit('loading', value);
        }
    },

    methods: {
        /**
         * load objects using PaginatedContentMixin.getPaginatedObjects()
         *
         * @return {Promise} resp
         */
        async loadObjects() {
            this.loading = true;

            let resp = await this.getPaginatedObjects();
            this.loading = false;

            return resp;
        },

        /**
         * send addedRelations to parent view
         *
         * @return {void}
         */
        appendRelations() {
            this.$emit('append-relations', this.pendingRelations);

            this.isVisible = false;
        },

        /**
         * handles ESC keyboard up event to hide current view
         *
         * @param {Event} event
         */
        handleKeyboard(event) {
            if (this.isVisible) {
                event.stopImmediatePropagation();
                event.preventDefault();
                this.hideRelationshipModal()
            }
        },

        /**
         * set component view's visibility to false
         * reset pendingRelations
         *
         * @return {void}
         */
        hideRelationshipModal() {
            this.pendingRelations = this.addedRelations;
            this.isVisible = false;
        },

        /**
         * helper function for template
         *
         * @return {Boolean} true if has at least a related object or a newly added object
         */
        hasElementsToShow() {
            const visible = this.objects.filter((obj) => {
                return !this.hideRelations.filter( (hidden) => obj.id === hidden.id).length;
            });
            return visible.length;
        },

        /**
         * helper function: convert array to string
         *
         * @param {Array} relations
         *
         * @return {String} string version of relations
         */
        relationFormatterHelper(relations, objectType) {
            let jsonString = '';
            try {
                jsonString = JSON.stringify(relations);
            } catch(err) {
                console.error(err);
            }
            return jsonString;
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

    }

});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/staggered-list.js":
/*!*****************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/staggered-list.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <staggered-list> component used for lists with staggered animation
 *
 */

const NAME = 'staggered';

/* harmony default export */ __webpack_exports__["default"] = ({
    template: `
        <transition-group appear
            name="${NAME}"
            v-on:enter="enter"
            v-on:after-enter="afterEnter">
            <slot></slot>
        </transition-group>`,

    props: {
        stagger: {
            type: String,
            default: () => 50,
        },
    },

    methods: {
        enter(el, done) {
            el.classList.remove(`${NAME}-enter-to`);
            el.classList.add(`${NAME}-enter`);
            const delay = this.getDelay(el);
            setTimeout(() => {
                this.$nextTick(() => {
                    el.classList.add(`${NAME}-enter`);
                    el.classList.remove(`${NAME}-enter-to`);
                    el.classList.remove(`${NAME}-enter-active`);
                });

                done();
            }, delay);
        },

        afterEnter(el) {
            this.$nextTick(() => {
                el.classList.remove(`${NAME}-enter`);
                el.classList.remove(`${NAME}-enter-to`);
            });
        },

        getDelay(el) {
            return el.dataset && el.dataset.index * this.stagger + 5;
        }
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/tree-view/tree-list/tree-list.js":
/*!********************************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/tree-view/tree-list/tree-list.js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-list> component used for ModulesPage -> View
 *
 * modes:
 * - single-choice tree
 * - multiple-choice tree
 *
 * @prop {Boolean} multipleChoice (default true)
 * @prop {String} captionField specify which field of the object is to be used a caption
 * @prop {String} childrenField specify which field of the object is to be used a children
 * @prop {Object} item object of this node
 * @prop {Array} relatedObjects list of already related Objects
 *
 */

/* harmony default export */ __webpack_exports__["default"] = ({
    name: 'tree-list',

    template: `
        <div
            class="tree-list-node"
            :class="treeListMode">

            <div v-if="!isRoot">
                <div v-if="multipleChoice"
                    class="node-element"
                    :class="{
                        'tree-related-object': isRelated,
                        'disabled': isCurrentObjectInPath,
                        'node-folder': isFolder,
                    }">

                    <span
                        @click.prevent.stop="toggle"
                        class="icon"
                        :class="nodeIcon"
                        ></span>
                    <input
                        type="checkbox"
                        :value="item"
                        v-model="related"
                    />
                    <label
                        @click.prevent.stop="toggle"
                        :class="isFolder ? 'is-folder' : ''"><: caption :></label>
                </div>
                <div v-else class="node-element"
                    :class="{
                        'tree-related-object': isRelated || stageRelated,
                        'was-related-object': isRelated && !stageRelated,
                        'disabled': isCurrentObjectInPath
                    }"

                    @click.prevent.stop="select">
                    <span
                        @click.prevent.stop="toggle"
                        class="icon"
                        :class="nodeIcon"
                        ></span>
                    <label><: caption :></label>
                </div>
            </div>
            <div :class="isRoot ? '' : 'node-children'" v-show="open" v-if="isFolder">
                <tree-list
                    @add-relation="addRelation"
                    @remove-relation="removeRelation"
                    @remove-all-relations="removeAllRelations"
                    v-for="(child, index) in item.children"
                    :key="index"
                    :item="child"
                    :multiple-choice="multipleChoice"
                    :related-objects="relatedObjects"
                    :object-id=objectId>
                </tree-list>
            </div>
        </div>
    `,

    data() {
        return {
            stageRelated: false,
            related: false,
            open: true,
        }
    },

    props: {
        multipleChoice: {
            type: Boolean,
            default: true,
        },
        captionField: {
            type: String,
            required: false,
            default: 'name',
        },
        childrenField: {
            type: String,
            required: false,
            default: 'children',
        },
        item: {
            type: Object,
            required: true,
            default: () => {},
        },
        relatedObjects: {
            type: Array,
            default: () => [],
        },
        objectId: {
            type: String,
            required: false,
        }
    },

    computed: {
        /**
         * caption used in label
         *
         * @return {String}
         */
        caption() {
            return this.item[this.captionField];
        },

        /**
         * check if current node is a folder
         *
         * @return {Boolean}
         */
        isFolder() {
            return this.item.children &&
                !!this.item.children.length;
        },

        /**
         * check if current node is a root node
         *
         * @return {Boolean}
         */
        isRoot() {
            return this.item.root || false;
        },

        /**
         * check if current node is already related
         *
         * @return {Boolean}
         */
        isRelated() {
            if (!this.item.id) {
                return false;
            }

            return !!this.relatedObjects.filter(related => related.id === this.item.id).length;
        },

        /**
         * check if current object is in item path
         */
        isCurrentObjectInPath() {
            return this.item && this.item.object && this.item.object.meta.path.indexOf(this.objectId) !== -1;
        },

        /**
         * compute correct icon css class name according to this node
         *
         * @return {String} css class name
         */
        nodeIcon() {
            let css = '';
            css += this.isFolder
                ? this.open
                    ? 'icon-down-dir'
                    : 'icon-right-dir'
                : 'unicode-branch'

            return css;
        },

        /**
         * compute correct css class name according to this node
         *
         * @return {String} css class name
         */
        treeListMode() {
            let css = [];
            if (this.isRoot) {
                css.push('root-node');
            }

            if (!this.multipleChoice) {
                css.push('tree-list-single-choice');
            } else {
                css.push('tree-list-multiple-choice');
            }

            if (this.isCurrentObject) {
                css.push('disabled');
            }

            return css.join(' ');
        }
    },

    watch: {
        /**
         * watch related used as model for tree-list in multiple-choice mode, used as model for checkboxes
         * set the stageRelated value
         *
         * @return {void}
         */
        related(value) {
            this.stageRelated = value;
        },

        /**
         * watch stageRelated used as model for tree-list in single-choice mode and triggers an event according to the state of t
         * - true: add-relation
         * - false: remove-relation
         *
         * @return {void}
         */
        stageRelated(value) {
            if (!this.item.object) {
                return;
            }
            if (value) {
                this.$emit('add-relation', this.item.object);
            } else {
                this.$emit('remove-relation', this.item.object);
            }
        },

        /**
         * watch relatedObjects and check if is already related
         *
         * @return {void}
         */
        relatedObjects() {
            this.related = this.isRelated;
        },
    },

    methods: {
        /**
         * toggle children visibility
         *
         * @return {void}
         */
        toggle() {
            if (this.isFolder) {
                this.open = !this.open;
            }
        },

        /**
         * triggers add-relation event in order to pass object to upper component
         * tree-view handles the addition
         *
         * @param {Object} rel
         *
         * @return {void}
         */
        addRelation(rel) {
            this.$emit('add-relation', rel);
        },

        /**
         * triggers remove-relation event in order to pass object to upper component
         * tree-view handles the removal
         *
         * @param {Object} rel
         *
         * @return {void}
         */
        removeRelation(rel) {
            this.$emit('remove-relation', rel);
        },

        /**
         * triggers remove-all-relations event in order to remove all pending relations
         *
         * @return {void}
         */
        removeAllRelations() {
            this.$emit('remove-all-relations');
        },

        /**
         * single-choice mode: select current tree entry for staging
         *
         * @return {void}
         */
        select() {
            // avoid user selecting same tree item (or in path) as current object
            if (this.isCurrentObjectInPath) {
                return;
            }
            // TO-DO handle folder removal from tree or folder as root

            // let oldValue = this.stageRelated;
            this.$emit('remove-all-relations');
            // if (oldValue) {
            //     this.$emit('add-relation', { id: null, type: 'folders'});
            // }
            this.stageRelated = !this.stageRelated;
        }
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/tree-view/tree-view.js":
/*!**********************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/tree-view/tree-view.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_components_relation_view_relationships_view_relationships_view__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/components/relation-view/relationships-view/relationships-view */ "./src/Template/Layout/js/app/components/relation-view/relationships-view/relationships-view.js");
/* harmony import */ var app_components_tree_view_tree_list_tree_list__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/components/tree-view/tree-list/tree-list */ "./src/Template/Layout/js/app/components/tree-view/tree-list/tree-list.js");
/* harmony import */ var sleep_promise__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! sleep-promise */ "./node_modules/sleep-promise/build/esm.mjs");
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName
 * @prop {Boolean} loadOnStart load content on component init
 * @prop {Boolean} multipleChoice
 *
 */





/* harmony default export */ __webpack_exports__["default"] = ({
    extends: app_components_relation_view_relationships_view_relationships_view__WEBPACK_IMPORTED_MODULE_0__["default"],
    components: {
        TreeList: app_components_tree_view_tree_list_tree_list__WEBPACK_IMPORTED_MODULE_1__["default"]
    },

    props: {
        relatedObjects: {
            type: Array,
            default: () => [],
        },
        loadOnStart: [Boolean, Number],
        multipleChoice: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {
            jsonTree: {},   // json tree version of the objects list based on path
        }
    },

    /**
     * load content if flag set to true after component is created
     *
     * @return {void}
     */
    created() {
        this.loadTree();
    },

    watch: {
        /**
         * watch pendingRelations used as a model for view's checkboxes and separates relations in
         * ones to be added and ones to be removed according to the already related objects Array
         *
         * @param {Array} pendingRels
         */
        pendingRelations(pendingRels) {
            // handles relations to be added
            let relationsToAdd = pendingRels.filter((rel) => {
                return !this.isRelated(rel.id);
            });

            if (!this.multipleChoice) {
                if(relationsToAdd.length) {
                    relationsToAdd = relationsToAdd[0];
                }
            }

            this.relationsData = this.relationFormatterHelper(relationsToAdd);

            // handles relations to be removes
            let relationsToRemove = this.relatedObjects.filter((rel) => {
                return !this.isPending(rel.id);
            });

            // emit event to pass data to parent
            this.$emit('remove-relations', relationsToRemove);
        },

        /**
         * watch objects and insert already related objects into pendingRelations
         *
         * @return {void}
         */
        objects() {
            this.pendingRelations = this.objects.filter((rel) => {
                return this.isRelated(rel.id);
            });
        },
    },

    methods: {
        /**
         * check loadOnStart prop and load content if set to true
         *
         * @return {void}
         */
        async loadTree() {
            if (this.loadOnStart) {
                var t = (typeof this.loadOnStart === 'number')? this.loadOnStart : 0;
                await Object(sleep_promise__WEBPACK_IMPORTED_MODULE_2__["default"])(t);
                await this.loadObjects();
                this.jsonTree = {
                    name: 'Root',
                    root: true,
                    object: {},
                    children: this.createTree(),
                };
            }
        },

        /**
         * add an object from prendingrelations Array if not present
         *
         * @event add-relation triggered from sub components
         *
         * @param {Object} related
         *
         * @return {void}
         */
        addRelation(related) {
            if (!related || !related.id === undefined) {
                console.error('[addRelation] needs first param (related) as {object} with property id set');
                return;
            }
            if (!this.containsId(this.pendingRelations, related.id)) {
                this.pendingRelations.push(related);
            }
        },

        /**
         * remove an object from pendingRelations Array
         *
         * @event remove-relation triggered from sub components
         *
         * @param {Object} related
         *
         * @return {void}
         */
        removeRelation(related) {
            if (!related || !related.id) {
                console.error('[removeRelation] needs first param (related) as {object} with property id set');
                return;
            }
            this.pendingRelations = this.pendingRelations.filter(pending => pending.id !== related.id);
        },

        /**
         * remove all related objects from pendingRelations Array
         *
         * @event remove-all-relations triggered from sub components
         *
         * @return {void}
         */
        removeAllRelations() {
            this.pendingRelations = [];
            this._setChildrenData(this, 'stageRelated', false);
        },

        /**
         * util function to set recursively all sub-components 'dataName' var with dataValue
         *
         * @param {Object} obj
         * @param {String} dataName
         * @param {any} dataValue
         */
        _setChildrenData(obj, dataName, dataValue) {
            if (obj !== undefined && dataName in obj) {
                obj[dataName] = dataValue;
            }

            obj.$children.forEach(child => {
                this._setChildrenData(child, dataName, dataValue);
            });
        },

        /**
         * create a json Tree from a list of objects with path
         *
         * @return {Object} json tree
         */
        createTree() {
            let jsonTree = [];
            this.objects.forEach((obj) => {
                let path = obj.meta.path && obj.meta.path.split('/');
                if (path.length) {
                    // Remove first blank element from the parts array.
                    path.shift();

                    // initialize currentLevel to root
                    let currentLevel = jsonTree;

                    path.forEach((id) => {
                        // check to see if the path already exists.
                        let existingPath = this.findPath(currentLevel, id);


                        if (existingPath) {
                            // The path to this item was already in the tree, so I set the current level to this path's children
                            currentLevel = existingPath.children;
                        } else {
                            // create a new node
                            let currentObj = obj;

                            // if current object is not the same as the discovered node get it from objects array
                            if (currentObj.id !== id) {
                                currentObj = this.findObjectById(id);
                            }

                            let newNode = {
                                id: id,
                                related: this.isRelated(id),
                                name: currentObj.attributes.title || '',
                                object: currentObj,
                                children: [],
                            };

                            currentLevel.push(newNode);
                            currentLevel = newNode.children;
                        }
                    });
                }
            });

            return jsonTree;
        },

        /**
         * check if part is already contained in the tree
         *
         * @param {Number} paths
         * @param {Number} part
         *
         * @return {Object|Boolean}
         */
        findPath(paths, part) {
            let path = paths.filter(path => path.id === part);
            return path.length ? path[0] : false;
        },

        /**
         * check if relatedObjects contains object with a specific id
         *
         * @param {Number} id
         *
         * @return {Boolean}
         */
        isRelated(id) {
            return this.relatedObjects.filter((relatedObject) => {
                return id === relatedObject.id;
            }).length ? true : false;
        },

        /**
         * check if pendingRelations contains object with a specific id
         *
         * @param {Number} id
         *
         * @return {Boolean}
         */
        isPending(id) {
            return this.pendingRelations.filter((pendingRelation) => {
                return id === pendingRelation.id;
            }).length ? true : false;
        },
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/directives/datepicker.js":
/*!*************************************************************!*\
  !*** ./src/Template/Layout/js/app/directives/datepicker.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flatpickr/dist/flatpickr.min */ "./node_modules/flatpickr/dist/flatpickr.min.js");
/* harmony import */ var flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flatpickr_dist_flatpickr_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flatpickr/dist/flatpickr.min.css */ "./node_modules/flatpickr/dist/flatpickr.min.css");
/* harmony import */ var flatpickr_dist_flatpickr_min_css__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flatpickr_dist_flatpickr_min_css__WEBPACK_IMPORTED_MODULE_1__);
// flatpickr

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <staggered-list> component used for lists with staggered animation
 *
 */




const datepickerOptions = {
    enableTime: false,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "F j, Y - H:i",
};

/* harmony default export */ __webpack_exports__["default"] = ({
    install(Vue) {
        Vue.directive('datepicker', {
            /**
             * create flatpicker instance when element is inserted
             *
             * @param {Object} element DOM object
             */
            inserted (element, dir, vueEl) {
                let options = datepickerOptions;

                if (vueEl.data && vueEl.data.attrs && vueEl.data.attrs.time) {
                    options.enableTime = vueEl.data.attrs.time;
                }

                try {
                    let datePicker = flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_0___default()(element, options);

                    let clearButton = document.createElement('span');
                    clearButton.classList.add('clear-button');
                    clearButton.innerHTML = '&times;';
                    clearButton.addEventListener('click', () => {
                        datePicker.clear();
                    });

                    element.parentElement.appendChild(clearButton);
                } catch (err) {
                    console.error(err);
                }
            },
        })
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/directives/jsoneditor.js":
/*!*************************************************************!*\
  !*** ./src/Template/Layout/js/app/directives/jsoneditor.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jsoneditor_dist_jsoneditor_min__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jsoneditor/dist/jsoneditor.min */ "./node_modules/jsoneditor/dist/jsoneditor.min.js");
/* harmony import */ var jsoneditor_dist_jsoneditor_min__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jsoneditor_dist_jsoneditor_min__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jsoneditor_dist_jsoneditor_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jsoneditor/dist/jsoneditor.min.css */ "./node_modules/jsoneditor/dist/jsoneditor.min.css");
/* harmony import */ var jsoneditor_dist_jsoneditor_min_css__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jsoneditor_dist_jsoneditor_min_css__WEBPACK_IMPORTED_MODULE_1__);
/**
 *
 * v-jsoneditor directive to activate jsoneditor on element
 *
 */




const jsonEditorOptions = {
    "mode": "code",
    "modes": ["tree", "code"],
    "history": true,
    "search": true,
    onChange: function (element) {
        if (element) {
            const json = element.jsonEditor.get();

            try {
                element.value = JSON.stringify(json);
            } catch(e) {
                console.error(e);
            }
        }
    },
    onModeChange: function(element ,a,b,c) {
        this.editor.setOptions({maxLines: Infinity});

        // element.jsonEditor.editor.setOptions({maxLines: Infinity});
    }
};

/* harmony default export */ __webpack_exports__["default"] = ({
    install(Vue) {
        Vue.directive('jsoneditor', {
            // element: null,

            /**
             * create jsoneditor instance when element is inserted
             *
             * @param {Object} element DOM object
             */
            inserted (element, binding, vnode, oldVnode) {
                const content = element.value;
                try {
                    const json = JSON.parse(content) || {};

                    if (json) {
                        element.style.display = "none";
                        let jsonEditor = document.createElement('div');
                        jsonEditor.className = "jsoneditor-container";
                        element.parentElement.insertBefore(jsonEditor, element);
                        element.jsonEditor = new jsoneditor_dist_jsoneditor_min__WEBPACK_IMPORTED_MODULE_0___default.a(jsonEditor, jsonEditorOptions);
                        element.jsonEditor.set(json);
                        // this.el = element;
                    }
                } catch (err) {
                    console.error(err);
                }
            },
        })
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/directives/richeditor.js":
/*!*************************************************************!*\
  !*** ./src/Template/Layout/js/app/directives/richeditor.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var config_config__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! config/config */ "./src/Template/Layout/js/config/config.js");
/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */



/* harmony default export */ __webpack_exports__["default"] = ({
            install(Vue) {
                Vue.directive('richeditor', {
            /**
             * When the bound element is inserted into the init CKeditor
             *
             * @param {Object} element DOM object
             */
            inserted (element) {
                const configKey = element.getAttribute('ckconfig');
                let loadedConfig = null;
                if (config_config__WEBPACK_IMPORTED_MODULE_0__["CkeditorConfig"]) {
                    loadedConfig = config_config__WEBPACK_IMPORTED_MODULE_0__["CkeditorConfig"][configKey];
                }

                CKEDITOR.replace(element, loadedConfig);
            },
        })
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/mixins/paginated-content.js":
/*!****************************************************************!*\
  !*** ./src/Template/Layout/js/app/mixins/paginated-content.js ***!
  \****************************************************************/
/*! exports provided: DEFAULT_PAGINATION, PaginatedContentMixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DEFAULT_PAGINATION", function() { return DEFAULT_PAGINATION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PaginatedContentMixin", function() { return PaginatedContentMixin; });
/**
 * Mixins: PaginatedContentMixin
 *
 *
 */

const DEFAULT_PAGINATION = {
    count: 1,
    page: 1,
    page_size: 20,
    page_count: 1,
}

const PaginatedContentMixin = {
    data() {
        return {
            objects: [],
            endpoint: null,

            pagination: DEFAULT_PAGINATION,
        }
    },

    methods: {
        /**
         * fetch paginated objects based on this.endpoint value
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server
         */
        getPaginatedObjects(autoload = true) {
            let baseUrl = window.location.href;

            if (this.endpoint) {
                let requestUrl = `${baseUrl}/${this.endpoint}`;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                }

                requestUrl = this.setPagination(requestUrl);

                return fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        let objects = (Array.isArray(json.data) ? json.data : [json.data]) || [];
                        if (!json.data) {
                            // api response with error
                            objects = [];
                        }

                        if (autoload) {
                            this.objects = objects;
                        }
                        this.pagination = json.meta && json.meta.pagination || this.pagination;

                        return objects;
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            } else {
                return Promise.reject();
            }
        },

        /**
         * Add pagination info to endpoint url
         *
         * @param {String} url
         *
         * @return {String} formatted url
         */
        setPagination(url) {
            let pagination = '';
            let qi = '?';
            const separator = '&';

            Object.keys(this.pagination).forEach((key, index) => {
                pagination += `${index ? separator : ''}${key}=${this.pagination[key]}`;
            });

            let hasQueryIdentifier = url.indexOf(qi) === -1;
            if (!hasQueryIdentifier) {
                qi = '&';
            }
            return `${url}${qi}${pagination}`;
        },

        /**
         * find object with specific id
         *
         * @param {Number} id
         *
         * @return {Object}
         */
        findObjectById(id) {
            let obj = this.objects.filter(o => o.id === id);
            return obj.length && obj[0];
        },

        /**
         * append more objects to current array of objects
         *
         * @param {Number} qty number of elements to load
         *
         * @return {Promise} repsonse from server
         */
        async loadMore(qty = DEFAULT_PAGINATION.page_size) {
            if (this.pagination.page_items < this.pagination.count) {
                let moreObjects = await this.nextPage(false);
                this.pagination.page_items = this.pagination.page_items + qty <= this.pagination.count ? this.pagination.page_items + qty : this.pagination.count;
                // this.pagination.page--;

                const last = this.objects.length;
                this.objects.splice(last, 0, ...moreObjects);
            }
        },


        toPage(i) {
            this.pagination.page = i || 1;
            return this.getPaginatedObjects(true);
        },

        /**
         * load first page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        firstPage(autoload = true) {
            if (this.pagination.page !== 1) {
                this.pagination.page = 1;

                return this.getPaginatedObjects(autoload);
            }

            return Promise.resolve([]);
        },

        /**
         * load last page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        lastPage(autoload = true) {
            if (this.pagination.page !== this.pagination.page_count) {
                this.pagination.page = this.pagination.page_count;

                return this.getPaginatedObjects(autoload);
            }

            return Promise.resolve([]);
        },

        /**
         * load next page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        nextPage(autoload = true) {
            if (this.pagination.page < this.pagination.page_count) {
                this.pagination.page = this.pagination.page + 1;

                return this.getPaginatedObjects(autoload);
            }

            return Promise.resolve([]);
        },

        /**
         * load previous page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        prevPage() {
            if (this.pagination.page > 1) {
                this.pagination.page = this.pagination.page - 1;

                return this.getPaginatedObjects();
            }

            return Promise.resolve();
        },

        /**
         * set Pagination page size
         *
         * @param {Number} size
         *
         * @return {void}
         */
        setPageSize(size) {
            this.pagination.page_size = size;
            this.pagination.page = 1;
        },
    }
}


/***/ }),

/***/ "./src/Template/Layout/js/app/pages/modules/index.js":
/*!***********************************************************!*\
  !*** ./src/Template/Layout/js/app/pages/modules/index.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Modules/index.twig
 *  - Element/Toolbar/filter.twig
 *  - Element/Toolbar/pagination.twig
 *
 *
 * <modules-index> component used for ModulesPage -> Index
 *
 */
// Vue.component('modules-index', {

/* harmony default export */ __webpack_exports__["default"] = ({
    /**
     * Component properties
     *
     * @type {Object} props properties
     */
    props: {
        ids: {
            type: String,
            default: () => [],
        },
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            allIds: [],
            selectedRows: [],
            status: '',
        };
    },

    /**
     * @inheritDoc
     */
    created() {
        try {
            this.allIds = JSON.parse(this.ids);
        } catch(error) {
            console.error(error);
        }
    },

    computed: {
        selectedIds() {
            return JSON.stringify(this.selectedRows);
        },
        allChecked() {
            return JSON.stringify(this.selectedRows.sort()) == JSON.stringify(this.allIds.sort());
        }
    },

    /**
     * component methods
     */
    methods: {
        /**
         * Click con check/uncheck all
         *
         * @return {void}
         */
        toggleAll() {
            if (this.allChecked) {
                this.unCheckAll();
            } else {
                this.checkAll();
            }
        },
        checkAll() {
            this.selectedRows = JSON.parse(JSON.stringify(this.allIds));
        },
        unCheckAll() {
            this.selectedRows = [];
        },

        /**
         * Submit bulk export form
         *
         * @return {void}
         */
        exportSelected() {
            if (this.selectedRows.length < 1) {
                return;
            }
            document.getElementById('form-export').submit();
        },

        /**
         * Submit bulk change status form
         *
         * @return {void}
         */
        setStatus(status, evt) {
            if (this.selectedRows.length < 1) {
                return;
            }
            this.status = status;
            this.$nextTick( () => {
                document.getElementById('form-status').submit();
            });
        },

        /**
         * Submit bulk trash form
         *
         * @return {void}
         */
        trash() {
            if (this.selectedRows.length < 1) {
                return;
            }
            if (confirm('Move ' + this.selectedRows.length + ' item to trash')) {
                document.getElementById('form-delete').submit();
            }
        },

        /**
         * selects a row when triggered from a container target that is parent of the relative checkbox
         *
         * @return {void}
         */
        selectRow(event) {
            if(event.target.type != 'checkbox') {
                event.preventDefault();
                var cb = event.target.querySelector('input[type=checkbox]');
                let position = this.selectedRows.indexOf(cb.value);
                if (position != -1) {
                    this.selectedRows.splice(position, 1);
                } else {
                    this.selectedRows.push(cb.value);
                }
            }
        }
    }
});




/***/ }),

/***/ "./src/Template/Layout/js/app/pages/modules/view.js":
/*!**********************************************************!*\
  !*** ./src/Template/Layout/js/app/pages/modules/view.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_components_property_view_property_view__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/components/property-view/property-view */ "./src/Template/Layout/js/app/components/property-view/property-view.js");
/* harmony import */ var app_components_relation_view_relation_view__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/components/relation-view/relation-view */ "./src/Template/Layout/js/app/components/relation-view/relation-view.js");
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */




/* harmony default export */ __webpack_exports__["default"] = ({
    components: {
        PropertyView: app_components_property_view_property_view__WEBPACK_IMPORTED_MODULE_0__["default"],
        RelationView: app_components_relation_view_relation_view__WEBPACK_IMPORTED_MODULE_1__["default"],
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            tabsOpen: true,
        };
    },

    computed: {
        keyEvents() {
            return {
                'esc': {
                    keyup: this.toggleTabs,
                },
            }
        }
    },

    methods: {
        toggleTabs() {
            return this.tabsOpen = !this.tabsOpen;
        }
    }
});




/***/ }),

/***/ "./src/Template/Layout/js/config/config.js":
/*!*************************************************!*\
  !*** ./src/Template/Layout/js/config/config.js ***!
  \*************************************************/
/*! exports provided: VueConfig, VueOptions, CkeditorConfig */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "VueConfig", function() { return VueConfig; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "VueOptions", function() { return VueOptions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "CkeditorConfig", function() { return CkeditorConfig; });
// Vue configs...

const VueConfig = {
    devtools: true,
}

const VueOptions = {
    delimiters: ['<:', ':>'],
}

// Vue.config.devtools = true;

// Custom delimiters, avoid `visual` conflict with Twig {{ }} and {% %}
// Vue.options.delimiters = ['<:', ':>'];

// CKeditor configs...

const CkeditorConfig = {
    configFull: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'editAttributes', items: [ 'Attr' ] },
            { name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Formula' ] },
            { name: 'tools', items: [ 'ShowBlocks', 'AutoCorrect' ] },
            { name: 'styles', items: [ 'Format' , 'Styles'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        ],
        allowedContent: true,
        language: BEDITA.currLang2,
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
    },

    configNormal: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'links', items: [ 'Link', 'Unlink'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        ],
        allowedContent: true,
        language: BEDITA.currLang2,
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
    },

    configSimple: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'links', items: [ 'Link', 'Unlink'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
            { name: 'tools', items: [ 'ShowBlocks' ] },
        ],
        allowedContent: true,
        language: BEDITA.currLang2,
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
    },
};


/***/ }),

/***/ "./src/Template/Layout/style.scss":
/*!****************************************!*\
  !*** ./src/Template/Layout/style.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************!*\
  !*** multi ./src/Template/Layout/js/app/app.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/Vallo/.bitnami/stackman/machines/xampp/volumes/root/htdocs/be4-web/src/Template/Layout/js/app/app.js */"./src/Template/Layout/js/app/app.js");


/***/ })

},[[0,"manifest","vendors"]]]);
//# sourceMappingURL=app.bundle.js.map