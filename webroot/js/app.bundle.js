(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["app"],{

/***/ "./src/Template/Layout/js/app/app.js":
/*!*******************************************!*\
  !*** ./src/Template/Layout/js/app/app.js ***!
  \*******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var libs_filters__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! libs/filters */ "./src/Template/Layout/js/libs/filters.js");
/* harmony import */ var config_config__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! config/config */ "./src/Template/Layout/js/config/config.js");
/* harmony import */ var Template_Layout_style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! Template/Layout/style.scss */ "./src/Template/Layout/style.scss");
/* harmony import */ var Template_Layout_style_scss__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(Template_Layout_style_scss__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var libs_bedita__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! libs/bedita */ "./src/Template/Layout/js/libs/bedita.js");
/* harmony import */ var app_pages_modules_index__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! app/pages/modules/index */ "./src/Template/Layout/js/app/pages/modules/index.js");
/* harmony import */ var app_pages_modules_view__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! app/pages/modules/view */ "./src/Template/Layout/js/app/pages/modules/view.js");
/* harmony import */ var app_pages_trash_index__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! app/pages/trash/index */ "./src/Template/Layout/js/app/pages/trash/index.js");
/* harmony import */ var app_pages_trash_view__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! app/pages/trash/view */ "./src/Template/Layout/js/app/pages/trash/view.js");
/* harmony import */ var app_pages_import_index__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! app/pages/import/index */ "./src/Template/Layout/js/app/pages/import/index.js");
/* harmony import */ var app_components_filter_box__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! app/components/filter-box */ "./src/Template/Layout/js/app/components/filter-box.js");
/* harmony import */ var app_components_relation_view_relations_add__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! app/components/relation-view/relations-add */ "./src/Template/Layout/js/app/components/relation-view/relations-add.js");
/* harmony import */ var app_components_edit_relation_params__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! app/components/edit-relation-params */ "./src/Template/Layout/js/app/components/edit-relation-params.js");
/* harmony import */ var app_directives_datepicker__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! app/directives/datepicker */ "./src/Template/Layout/js/app/directives/datepicker.js");
/* harmony import */ var app_directives_jsoneditor__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! app/directives/jsoneditor */ "./src/Template/Layout/js/app/directives/jsoneditor.js");
/* harmony import */ var app_directives_richeditor__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! app/directives/richeditor */ "./src/Template/Layout/js/app/directives/richeditor.js");
/* harmony import */ var v_hotkey__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! v-hotkey */ "./node_modules/v-hotkey/index.js");
/* harmony import */ var v_hotkey__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(v_hotkey__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var sleep_promise__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! sleep-promise */ "./node_modules/sleep-promise/build/esm.mjs");

























const _vueInstance = new vue__WEBPACK_IMPORTED_MODULE_0___default.a({
    el: 'main',

    components: {
        ModulesIndex: app_pages_modules_index__WEBPACK_IMPORTED_MODULE_5__["default"],
        ModulesView: app_pages_modules_view__WEBPACK_IMPORTED_MODULE_6__["default"],
        TrashIndex: app_pages_trash_index__WEBPACK_IMPORTED_MODULE_7__["default"],
        TrashView: app_pages_trash_view__WEBPACK_IMPORTED_MODULE_8__["default"],
        ImportView: app_pages_import_index__WEBPACK_IMPORTED_MODULE_9__["default"],
        RelationsAdd: app_components_relation_view_relations_add__WEBPACK_IMPORTED_MODULE_11__["default"],
        FilterBoxView: app_components_filter_box__WEBPACK_IMPORTED_MODULE_10__["default"],
        EditRelationParams: app_components_edit_relation_params__WEBPACK_IMPORTED_MODULE_12__["default"],
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
            editingRelationParams: null,

            urlFilterQuery: {
                q: '',
            },

            pagination: {
                page: '',
                page_size: '100',
            }
        }
    },

    /**
     * properties or methods available for injection into its descendants
     * (inject: ['property'])
     */
    provide() {
        return {
            requestPanel: (...args) => this.requestPanel(...args),
            closePanel: (...args) => this.closePanel(...args),
            returnDataFromPanel: (...args) => this.returnDataFromPanel(...args),
        }
    },

    /**
     * setup Vue instance before creation
     *
     * @return {void}
     */
    beforeCreate() {
        // Register directives
        vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(app_directives_jsoneditor__WEBPACK_IMPORTED_MODULE_14__["default"]);
        vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(app_directives_datepicker__WEBPACK_IMPORTED_MODULE_13__["default"]);
        vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(app_directives_richeditor__WEBPACK_IMPORTED_MODULE_15__["default"]);
        vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(v_hotkey__WEBPACK_IMPORTED_MODULE_16___default.a);

        // load BEplugins's components
        libs_bedita__WEBPACK_IMPORTED_MODULE_4__["BELoader"].loadBeditaPlugins();
    },

    created() {
        this.vueLoaded = true;

        // load url params when component initialized
        this.loadUrlParams();
    },

    watch: {
        panelIsOpen(value) {
            var cl = document.querySelector('html').classList;
            if (value) {
                cl.add('is-clipped');
            } else {
                cl.remove('is-clipped');
            }
        },

        /**
         * watch pageSize variable and update pagination.page_size accordingly
         *
         * @param {Number} value page size number
         */
        pageSize(value) {
            this.pagination.page_size = value;
        }
    },

    mounted: function () {
        this.$nextTick(function () {
            if(BEDITA.template == 'view') {
                this.alertBeforePageUnload();
            }
        })
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
            this.urlFilterQuery = filter;
            this.page = '';
            this.applyFilters(this.urlFilterQuery);
        },

        /**
         * listen to FilterBoxView event filter-update-page-size
         *
         * @param {Number} pageSize
         *
         * @return {void}
         */
        onUpdatePageSize(pageSize) {
            this.pageSize = pageSize;
            this.page = '';
            this.applyFilters(this.urlFilterQuery);
        },

        /**
         * listen to FilterBoxView event filter-update-current-page
         *
         * @param {Number} page
         *
         * @return {void}
         */
        onUpdateCurrentPage(page) {
            this.page = page;
            this.applyFilters(this.urlFilterQuery);
        },

        /**
         * on page click:
         * - if panel is open, close it and stop event propagation
         * - if panel is closed do nothing
         *
         * @return {void}
         */
        pageClick(event) {
            // temporary comment: we do not want that panel is closed, when it contains pagination...
            // if (this.panelIsOpen) {
            //     this.closePanel();
            //     event.preventDefault();
            //     event.stopPropagation();
            // }
        },

        /**
         * return data from panel
         *
         * @param {Object} data
         *
         * @return {void}
         */
        returnDataFromPanel(data) {
            this.closePanel();

            // return data from RelationsAdd view component
            if (data.action === 'add-relation') {
                this.$refs["moduleView"]
                    .$refs[data.relationName]
                    .$refs["relation"].appendRelations(data.objects);
            }

            // return data from EditRelationParams view component
            if (data.action === 'edit-params') {
                const relationName = data.item.name;
                this.$refs["moduleView"]
                    .$refs[relationName]
                    .$refs["relation"].updateRelationParams(data.item);
            }
        },

        /**
         * close panel and clear data
         *
         * @return {void}
         */
        closePanel() {
            this.panelIsOpen = false;
            this.addRelation = {
                name: '',
                alreadyInView: [],
            };

            this.editingRelationParams = null;
        },

        /**
         * request panel and pass data
         *
         * @param {Object} data
         */
        requestPanel(data) {
            this.panelIsOpen = true;

            // open panel for relations add
            if(this.panelIsOpen && data.relation && data.relation.name) {
                this.addRelation = data.relation;
            } else if (this.panelIsOpen && data.editRelationParams && data.editRelationParams.name) {
                this.editingRelationParams = data.editRelationParams;
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
                    this.urlFilterQuery = { q: matches[0] };
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
         * reset queryString in search keeping pagination options
         *
         * @returns {void}
         */
        resetFilters() {
            this.page = '';
            this.pageSize = 100;
            let filter = {
                q: '',
            }
            this.applyFilters(filter);
        },

        /**
         * apply page filters such as query string or pagination
         *
         * @params {Object} filters filters object
         *
         * @returns {void}
         */
        applyFilters(filters) {
            let url = this.buildUrlParams({
                q: filters.q,
                page_size: this.pageSize,
                page: this.page,
                sort: this.sort,
            });
            window.location.replace(url);
        },

        /**
         * alerts onbeforeunload if forms changed and it's not a submit
         *
         * @returns {void}
         */
        alertBeforePageUnload() {
            var forms = [...document.querySelectorAll('form')];
            forms.forEach((form) => {
                form.addEventListener('change', () => {
                    form.changed = true;
                });
                form.addEventListener('submit', (ev) => {
                    if (form.action.endsWith('/delete')) {
                        if (!confirm("Do you really want to trash the object?")) {
                            ev.preventDefault();
                            return;
                        }
                    }
                    form.submitting = true;
                });
            });

            window.onbeforeunload = function() {
                if (forms.some((f) => f.changed) && !forms.some((f) => f.submitting)) {
                    return "There are unsaved changes, are you sure you want to leave page?";
                }
            }
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

/***/ "./src/Template/Layout/js/app/components/children-view/children-view.js":
/*!******************************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/children-view/children-view.js ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var app_components_relation_view_relation_view__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/components/relation-view/relation-view */ "./src/Template/Layout/js/app/components/relation-view/relation-view.js");
/**
 *  Extends RelationView: handles children relation
 *
 *  Templates that uses this component (directly or indirectly):
 *  Template/Elements/Form/children.twig
 *
 * <children-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName name of the relation used by the PaginatiedContentMixin
 * @prop {Boolean} loadOnStart load content on component init
 *
 */




/* harmony default export */ __webpack_exports__["default"] = ({
    extends: app_components_relation_view_relation_view__WEBPACK_IMPORTED_MODULE_1__["default"],

    data() {
        return {
            positions: {},
        }
    },

    methods: {

        /**
         * update relation position and stage for saving
         *
         * @param {Object} related related object
         *
         * @returns {void}
         */
        updatePosition(related) {
            const oldPosition = related.meta.relation.position;
            const newPosition = this.positions[related.id] !== '' ? this.positions[related.id] : undefined;
            if (newPosition !== oldPosition) {
                // try to deep copy the object
                try {
                    const copy = JSON.parse(JSON.stringify(related));
                    copy.meta.relation.position = newPosition;
                    this.modifyRelation(copy);
                } catch (exp) {
                    // silent error
                    console.error('[ChildrenView -> updatePosition] something\'s wrong with the data');
                }
            } else {
                this.removeModifiedRelations(related.id);
            }
        },
    }
});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/edit-relation-params.js":
/*!***********************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/edit-relation-params.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/**
 * View component used for editing relations param
 *
 */


/* harmony default export */ __webpack_exports__["default"] = ({
    // injected methods provided by Main App
    inject: ['returnDataFromPanel','closePanel'],

    props: {
        relation: {
            type: Object,
            default: () => {},
        },
    },

    computed: {
        relatedStatus() {
            return this.relation.related.attributes.status;
        },
        relatedType() {
            let type = '(not available)';
            if (this.relation && this.relation.related) {
                type = this.relation.related.type;
            }

            return type;
        },
        relatedName() {
            let name = '(empty)';
            if (this.relation && this.relation.related) {
                name = this.relation.related.attributes.title;
            }

            return name;
        }
    },

    data() {
        return {
            oldParams: {},
            editingParams: {},
            priority: null,
            isModified: false,
        }
    },

    watch: {
        relation(value) {
            if (value) {
                Object.assign(this.oldParams, value.related.meta.relation.params);
                Object.assign(this.editingParams, value.related.meta.relation.params);

                this.priority = value.related.meta.relation.priority;
            } else {
                this.oldParams = {};
                this.editingParams = {};
                this.isModified = false;
                this.priority = null;
            }
        },
    },

    methods: {
        saveParams() {
            if (Object.keys(this.editingParams).length) {
                this.relation.related.meta.relation.params = this.editingParams;
            } else {
                delete this.relation.related.meta.relation.params;
            }
            this.relation.related.meta.relation.priority = this.priority;
            this.closeParamsView();
            this.returnDataFromPanel({
                action: 'edit-params',
                item: this.relation,
            });
        },

        closeParamsView() {
            this.editingParams = {};
            this.closePanel();
        },

        checkParams() {
            this.isModified = !!Object.keys(this.editingParams).filter((index) => {
                return this.editingParams[index] !== '' && this.editingParams[index] !== this.oldParams[index];
            }).length || this.relation.related.meta.relation.priority !== this.priority;
        },
    },
});


/***/ }),

/***/ "./src/Template/Layout/js/app/components/filter-box.js":
/*!*************************************************************!*\
  !*** ./src/Template/Layout/js/app/components/filter-box.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/mixins/paginated-content */ "./src/Template/Layout/js/app/mixins/paginated-content.js");
/**
 * Filter Box View component
 *
 * allows to filter a list of objects with a text field and a pagination toolbar
 *
 * <filter-box-view> component
 *
 * @prop {String} applyFilterLabel
 * @prop {String} resetFilterLabel
 * @prop {String} objectsLabel
 * @prop {String} pageSizesLabel
 * @prop {String} placeholder
 * @prop {Boolean} showFilterButtons
 * @prop {Object} initFilter
 * @prop {Object} pagination
 * @prop {String} configPaginateSizes
 */



/* harmony default export */ __webpack_exports__["default"] = ({
    template:
    `
        <nav class="pagination has-text-size-smallest" :class="pagination.count > 4 && 'show-pagination'">

            <div class="pagination-items">
                <span><: pagination.count :> <: objectsLabel :></span>
            </div>

            <div class="filter-search">
                <span>
                    <input type="text" :placeholder="placeholder" v-model="filter" @keyup.enter.prevent.stop="applyFilter()"/>
                </span>

                <button v-show="showFilterButtons" name="applysearch" @click.prevent="applyFilter()"><: applyFilterLabel :></button>
                <button v-show="showFilterButtons" name="resetsearch" @click.prevent="resetFilter()"><: resetFilterLabel :></button>
            </div>

            <div class="page-size">
                <span><: pageSizesLabel :>:</span>
            </div>

            <div class="pagination-buttons">
                <select class="page-size-selector has-background-gray-700 has-border-gray-700 has-text-gray-200 has-text-size-smallest has-font-weight-light" v-model="pageSize">
                    <option v-for="size in paginateSizes"><: size :></option>
                </select>

                <div class="pages-buttons full-layout" v-if="isFullPaginationLayout">
                    <button
                        v-for="i in pagination.page_count" :key="i"
                        v-bind:class="pagination.page == i? '' : 'is-dark'"
                        v-bind:style="pagination.page == i? 'pointer-events: none' : ''"
                        class="has-text-size-smallest" @click.prevent="changePage(i)"><: i :>
                    </button>
                </div>

                <div class="pages-buttons full-layout" v-if="!isFullPaginationLayout">
                    <!-- first page --->
                    <button v-if="pagination.page > 1" class="has-text-size-smallest" @click.prevent="changePage(1)"><: 1 :></button>

                    <!-- delimiter --->
                    <span v-if="pagination.page > 3" class="pages-delimiter">...</span>

                    <!-- prev page --->
                    <button v-if="pagination.page > 2" class="has-text-size-smallest" @click.prevent="changePage(pagination.page - 1)"><: pagination.page - 1:></button>

                    <!-- current page --->
                    <button class="is-dark current-page has-text-size-smallest" @click.prevent="changePage(pagination.page)"><: pagination.page :></button>

                    <!-- next page --->
                    <button v-if="pagination.page < pagination.page_count-1" class="has-text-size-smallest" @click.prevent="changePage(pagination.page + 1)"><: pagination.page + 1:></button>

                    <!-- delimiter --->
                    <span v-if="pagination.page < pagination.page_count-2" class="pages-delimiter">...</span>

                    <!-- last page --->
                    <button v-if="pagination.page < pagination.page_count" class="has-text-size-smallest" @click.prevent="changePage(pagination.page_count)"><: pagination.page_count :></button>
                </div>

            </div>
        </nav>
    `,

    props: {
        applyFilterLabel: {
            type: String,
            default: 'Apply'
        },
        resetFilterLabel: {
            type: String,
            default: 'Reset',
        },
        objectsLabel: {
            type: String,
            default: 'Objects',
        },
        pageSizesLabel: {
            type: String,
            default: 'Size',
        },
        placeholder: {
            type: String,
            default: 'Search',
        },
        showFilterButtons: {
            type: Boolean,
            default: true,
        },
        initFilter: {
            type: Object,
            default: () => {},
        },
        pagination: {
            type: Object,
            default: () => app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_0__["DEFAULT_PAGINATION"],
        },
        configPaginateSizes: {
            type: String,
            default: '[]',
        },
    },

    data() {
        return {
            filter: '', // Text string filter
            queryFilter: this.initFilter, // QueryFilter Object
            timer: null,

            pageSize: this.pagination.page_size, // pageSize value for pagination page size
        }
    },

    computed: {
        paginateSizes() {
            return JSON.parse(this.configPaginateSizes);
        },

        /**
         * check which navigation layout needs to be rendered
         *
         * @return {void}
         */
        isFullPaginationLayout() {
            return this.pagination.page_count > 1 && this.pagination.page_count <= 7;
        }
    },

    watch: {
        /**
         * watcher for pageSize variable, change pageSize and reload relations
         *
         * @param {Number} value
         */
        pageSize(value) {
            this.$emit('filter-update-page-size', this.pageSize);

        },

        /**
         * watcher for text filter
         * if value is more than 3 chars, emits a filter-objects event with queryFilter as params
         *
         * @param {String} value The filter string
         *
         * @emits Event#filter-objects
         *
         * @return {void}
         */
        filter(value) {
            this.filter = value;
            this.queryFilter = {
                q: this.filter
            };

            clearTimeout(this.timer);
            if (value.length >= 3 || value.length == 0) {
                this.timer = setTimeout(() => {
                    this.$emit('filter-objects', this.queryFilter);
                }, 300);
            }
        },
    },

    mounted() {
        /**
         * init filter from queryFilter
         */
        if (this.queryFilter !== undefined) {
            this.filter = this.queryFilter.q || '';
        }
    },

    methods: {
        /**
         * apply filters
         *
         * @emits Event#filter-objects-submit
         */
        applyFilter() {
            this.$emit('filter-objects-submit', this.queryFilter);
        },

        /**
         * reset filters
         *
         * @emits Event#filter-reset
         */
        resetFilter() {
            this.$emit('filter-reset');
        },

        /**
         * change page with index {index}
         *
         * @param {Number} index page number
         *
         * @emits Event#filter-update-current-page
         */
        changePage(index) {
            this.$emit('filter-update-current-page', index);
        }
    }
});


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
/* harmony import */ var app_components_children_view_children_view__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/components/children-view/children-view */ "./src/Template/Layout/js/app/components/children-view/children-view.js");
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
        ChildrenView: app_components_children_view_children_view__WEBPACK_IMPORTED_MODULE_1__["default"],
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
/* harmony import */ var app_components_filter_box__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! app/components/filter-box */ "./src/Template/Layout/js/app/components/filter-box.js");
/* harmony import */ var app_components_tree_view_tree_view__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! app/components/tree-view/tree-view */ "./src/Template/Layout/js/app/components/tree-view/tree-view.js");
/* harmony import */ var sleep_promise__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! sleep-promise */ "./node_modules/sleep-promise/build/esm.mjs");
/* harmony import */ var flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flatpickr/dist/flatpickr.min */ "./node_modules/flatpickr/dist/flatpickr.min.js");
/* harmony import */ var flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! app/mixins/paginated-content */ "./src/Template/Layout/js/app/mixins/paginated-content.js");
/* harmony import */ var app_mixins_relation_schema__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! app/mixins/relation-schema */ "./src/Template/Layout/js/app/mixins/relation-schema.js");
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
    // injected methods provided by Main App
    inject: ['requestPanel', 'closePanel'],
    mixins: [ app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_6__["PaginatedContentMixin"], app_mixins_relation_schema__WEBPACK_IMPORTED_MODULE_7__["RelationSchemaMixin"] ],

    components: {
        StaggeredList: app_components_staggered_list__WEBPACK_IMPORTED_MODULE_0__["default"],
        RelationshipsView: app_components_relation_view_relationships_view_relationships_view__WEBPACK_IMPORTED_MODULE_1__["default"],
        FilterBoxView: app_components_filter_box__WEBPACK_IMPORTED_MODULE_2__["default"],
        TreeView: app_components_tree_view_tree_view__WEBPACK_IMPORTED_MODULE_3__["default"],
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

                await Object(sleep_promise__WEBPACK_IMPORTED_MODULE_4__["default"])(t);
                if (this.relationSchema === null) {
                    await this.getRelationData();
                }

                await this.loadRelatedObjects();
            }
        },

        /**
         * call PaginatedContentMixin.getPaginatedObjects() method and handle loading
         *
         * @param {Object} filter
         *
         * @return {Boolean} response;
         */
        async loadRelatedObjects(filter = {}) {
            this.loading = true;
            let resp = await this.getPaginatedObjects(true, filter);
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
            this.$el.dispatchEvent(new Event('change', { bubbles: true }));
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
                return flatpickr_dist_flatpickr_min__WEBPACK_IMPORTED_MODULE_5___default.a.formatDate(new Date(value), 'Y-m-d h:i K');
            }

            return value;
        },

        /**
         * go to specific page
         *
         * @param {Number} page number
         *
         * @return {Promise} repsonse from server with new data
         */
        async toPage(page, filter) {
            this.loading = true;
            let resp =  await app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_6__["PaginatedContentMixin"].methods.toPage.call(this, page, filter);
            this.loading = false;
            return resp;
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
/* harmony import */ var app_components_filter_box__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/components/filter-box */ "./src/Template/Layout/js/app/components/filter-box.js");
/* harmony import */ var app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! app/mixins/paginated-content */ "./src/Template/Layout/js/app/mixins/paginated-content.js");
/* harmony import */ var decamelize__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! decamelize */ "./node_modules/decamelize/index.js");
/* harmony import */ var decamelize__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(decamelize__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var sleep_promise__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! sleep-promise */ "./node_modules/sleep-promise/build/esm.mjs");
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relations-add> component used for Panel
 *
 */






/* harmony default export */ __webpack_exports__["default"] = ({
    inject: ['returnDataFromPanel', 'closePanel'],      // injected methods provided by Main App

    mixins: [ app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_1__["PaginatedContentMixin"] ],

    components: {
        FilterBoxView: app_components_filter_box__WEBPACK_IMPORTED_MODULE_0__["default"],
    },

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

            activeFilter: {},
            loading: false,
        };
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
                // clear objects when relationName is empty (panel closed)
                if (newVal === '') {
                    Object(sleep_promise__WEBPACK_IMPORTED_MODULE_3__["default"])(500).then(() => this.objects = []);
                }
            },
        },

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
            this.loadObjects(this.activeFilter);
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
         * Return true if specified pagination page link must be shown
         *
         * @param {Number} page The pagination page number
         * @return {boolean} True if show page link
         */
        paginationPageLinkVisible(page) {
            if (this.pagination.page_count <= 7) { // show till 7 links
                return true;
            }
            if (page === 1 || page === this.pagination.page_count) { // show first and last page link
                return true;
            }
            if ( (page >= this.pagination.page-1) && page <= this.pagination.page+1) { // show previous and next page link
                return true;
            }

            return false;
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
         * @emits Event#count
         *
         * @param {Object} filter filter object
         *
         * @return {Promise} repsonse from server
         */
        async loadObjects(filter) {
            this.objects = [];
            this.loading = true;
            let response = await this.getPaginatedObjects(true, filter);
            this.loading = false;
            this.$emit('count', this.pagination.count);

            return response;
        },

        /**
         * Go to specific page
         *
         * @param {Number} page The page number
         * @param {Object} filter filter object
         *
         * @return {Promise} The response from server with new data
         */
        async toPage(page ,filter) {
            this.objects = [];
            this.loading = true;
            let response =  await app_mixins_paginated_content__WEBPACK_IMPORTED_MODULE_1__["PaginatedContentMixin"].methods.toPage.call(this, page, filter);
            this.loading = false;

            return response;
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




const dateTimePickerOptions = {
    enableTime: true,
    dateFormat: 'Y-m-d H:i',
    altInput: true,
    altFormat: 'F j, Y - H:i',
    animate: false,
};

const datepickerOptions = {
    enableTime: false,
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'F j, Y',
    animate: false,
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

                if (vueEl.data && vueEl.data.attrs && vueEl.data.attrs.time === 'true') {
                    options = dateTimePickerOptions;
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
/* harmony import */ var jsoneditor_dist_jsoneditor_minimalist__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jsoneditor/dist/jsoneditor-minimalist */ "./node_modules/jsoneditor/dist/jsoneditor-minimalist.js");
/* harmony import */ var jsoneditor_dist_jsoneditor_minimalist__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jsoneditor_dist_jsoneditor_minimalist__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jsoneditor_dist_jsoneditor_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jsoneditor/dist/jsoneditor.min.css */ "./node_modules/jsoneditor/dist/jsoneditor.min.css");
/* harmony import */ var jsoneditor_dist_jsoneditor_min_css__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jsoneditor_dist_jsoneditor_min_css__WEBPACK_IMPORTED_MODULE_1__);
/**
 *
 * v-jsoneditor directive to activate jsoneditor on element
 *
 */




const options = {
    mode: 'code',
    modes: ['tree', 'code'],
    history: true,
    search: true,
};

/* harmony default export */ __webpack_exports__["default"] = ({
    install(Vue) {
        Vue.directive('jsoneditor', {
            /**
             * create jsoneditor instance when element is inserted
             *
             * @param {Object} element DOM object
             */
            inserted (element) {
                const content = element.value;
                try {
                    const json = JSON.parse(content) || {};

                    if (json) {
                        element.style.display = "none";
                        let container = document.createElement('div');
                        container.className = 'jsoneditor-container';
                        element.parentElement.insertBefore(container, element);
                        let editorOptions = Object.assign(options, {
                            onChange: function () {
                                try {
                                    const json = element.jsonEditor.get();
                                    element.value = JSON.stringify(json);
                                    console.info('valid json :)');
                                } catch(e) {
                                    console.warn('still not valid json');
                                }
                            },
                        });
                        element.jsonEditor = new jsoneditor_dist_jsoneditor_minimalist__WEBPACK_IMPORTED_MODULE_0___default.a(container, editorOptions);
                        element.jsonEditor.set(json);
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
                let loadedConfig = {};
                if (config_config__WEBPACK_IMPORTED_MODULE_0__["CkeditorConfig"]) {
                    loadedConfig = config_config__WEBPACK_IMPORTED_MODULE_0__["CkeditorConfig"][configKey];
                }

                let editor = CKEDITOR.replace(element, loadedConfig);
                editor.on('change', () => {
                    element.value = editor.getData();
                    element.dispatchEvent(new Event('change', {
                        bubbles: true,
                    }));
                });
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
            query: {},
            formatObjetsFilter: ['params', 'priority', 'position', 'url'],
        }
    },

    methods: {
        /**
         * fetch paginated objects based on this.endpoint value
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         * @param {Object} query The query filter object
         *
         * @return {Promise} repsonse from server
         */
        getPaginatedObjects(autoload = true, query = {}) {
            let baseUrl = window.location.href;

            if (this.endpoint) {
                if (query) {
                    this.query = query;
                }
                let requestUrl = `${baseUrl}/${this.endpoint}`;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                };

                requestUrl = this.getUrlWithPaginationAndQuery(requestUrl);

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
         * format objects for api calls
         *
         * @param {Array} objects
         *
         * @returns {Array} of formatted objects
         */
        formatObjects(objects) {
            if (objects === undefined) {
                return [];
            }
            const formattedObjects = [];

            objects.forEach((obj) => {
                let formattedObj = {};
                // keep id and type
                formattedObj.id = obj.id;
                formattedObj.type = obj.type;

                // search for meta.relation using this.formatObjectsFilter
                const metaRelation = obj.meta.relation;
                if (metaRelation) {
                    let formattedMeta = {};
                    this.formatObjetsFilter.forEach((filter) => {
                        if (metaRelation[filter]) {
                            formattedMeta[filter] = metaRelation[filter];
                        }
                    });

                    if (Object.keys(formattedMeta).length) {
                        formattedObj.meta = { relation: formattedMeta };
                    }
                }
                formattedObjects.push(formattedObj);
            });

            return formattedObjects;
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
         * Get formatted url with query filter
         *
         * @param {String} url The endpoint url
         * @return {String} The formatted url
         */
        getUrlWithPaginationAndQuery(url) {
            let queryString = '';
            let qi = '?';
            const separator = '&';

            Object.keys(this.pagination).forEach((key, index) => {
                queryString += `${index ? separator : ''}${key}=${this.pagination[key]}`;
            });
            if (queryString.length > 1) {
                queryString += separator;
            }
            Object.keys(this.query).forEach((key, index) => {
                queryString += `${index ? separator : ''}${key}=${this.query[key]}`;
            });

            let hasQueryIdentifier = url.indexOf(qi) === -1;
            if (!hasQueryIdentifier) {
                qi = '&';
            }
            return `${url}${qi}${queryString}`;
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

        /**
         * Load page by page number and query string
         *
         * @param {Number} page The page number
         * @param {Object} query The query object (i.e. { q: 'search me' })
         */
        toPage(page, query = {}) {
            this.pagination.page = page || 1;
            return this.getPaginatedObjects(true, query);
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

/***/ "./src/Template/Layout/js/app/mixins/relation-schema.js":
/*!**************************************************************!*\
  !*** ./src/Template/Layout/js/app/mixins/relation-schema.js ***!
  \**************************************************************/
/*! exports provided: RelationSchemaMixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RelationSchemaMixin", function() { return RelationSchemaMixin; });
/**
 * Mixins: RelationSchemaMixin
 *
 * @prop {Boolean} tabOpen determines whether the property content is visible or not
 * @prop {String} label label of the property view
 * @
 *
 */

const RelationSchemaMixin = {
    data() {
        return {
            relationData: null,
            relationSchema: null,
        }
    },

    methods: {
        /**
         * retrieve json data for relation (this.relationName) and extrat params json schema
         *
         * @returns {Promise} promise with schema
         *
         */
        getRelationData() {
            let baseUrl = window.location.href;

            if (this.relationName) {
                // AppController endpoint for ajax request
                let requestUrl = `${baseUrl}/relationData/${this.relationName}`;

                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                };

                if (this.relationData) {
                    return Promise.resolve(this.relationData);
                }

                return fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        if (json.data && json.data.attributes) {
                            this.relationData = json.data.attributes;
                            this.relationSchema = this.getRelationSchema();
                            return this.relationData;
                        }
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            } else {
                console.error('[RelationSchemaMixin] relationName not defined - can\'t load relation schema');
                return Promise.reject();
            }
        },

        /**
         * helper function to check if relation schema is loaded and relations has params
         *
         * @returns {Boolean} true if relation has params
         *
         */
        relationHasParams() {
            return this.relationData !== null && !!this.getRelationSchema();
        },

        /**
         * extract params json schema from relationData
         *
         * @returns {Object} params json schema
         *
         */
        getRelationSchema() {
            if (this.relationSchema === null) {
                this.relationSchema = this.relationData !== null && this.relationData.params && this.relationData.params.properties;
            }
            return this.relationSchema;
        },

        /**
         * helper function to get params value from an object according to bedita api convention
         *
         * @param {Object} object
         * @param {String} paramKey
         *
         * @returns {Boolean} true if relation has params
         *
         */
        getParamHelper(object, paramKey) {
            return object.meta.relation.params && object.meta.relation.params[paramKey] || null;
        },
    }
}


/***/ }),

/***/ "./src/Template/Layout/js/app/pages/import/index.js":
/*!**********************************************************!*\
  !*** ./src/Template/Layout/js/app/pages/import/index.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Import/index.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

/* harmony default export */ __webpack_exports__["default"] = ({
    data() {
        return {
            fileName: '',
        };
    },

    computed: {
    },

    methods: {
        onFileChanged(e) {
            this.fileName = e.target.files[0].name;
        }
    }
});


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

/***/ "./src/Template/Layout/js/app/pages/trash/index.js":
/*!*********************************************************!*\
  !*** ./src/Template/Layout/js/app/pages/trash/index.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_pages_modules_index__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/pages/modules/index */ "./src/Template/Layout/js/app/pages/modules/index.js");
/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Trash/index.twig
 *  - Element/Toolbar/filter.twig
 *  - Element/Toolbar/pagination.twig
 *
 * <trash-index> component used for TrashPage -> Index
 *
 * @extends ModulesIndex
 */



/* harmony default export */ __webpack_exports__["default"] = ({
    extends: app_pages_modules_index__WEBPACK_IMPORTED_MODULE_0__["default"],
    methods: {
        /**
         * Submit bulk restore form
         *
         * @return {void}
         */
        restoreItem() {
            if (this.selectedRows.length < 1) {
                return;
            }
            document.getElementById('form-restore').submit();
        },

        /**
         * Submit bulk delete form
         *
         * @return {void}
         */
        deleteItem() {
            if (this.selectedRows.length < 1) {
                return;
            }
            if (confirm('Confirm deletion of ' + this.selectedRows.length + ' item from the trash')) {
                document.getElementById('form-delete').submit();
            }
        },
    }
});




/***/ }),

/***/ "./src/Template/Layout/js/app/pages/trash/view.js":
/*!********************************************************!*\
  !*** ./src/Template/Layout/js/app/pages/trash/view.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var app_pages_modules_view__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! app/pages/modules/view */ "./src/Template/Layout/js/app/pages/modules/view.js");
/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Trash/view.twig
 *
 * <trash-view> component used for TrashPage -> View
 *
 * @extends ModulesView
 */




/* harmony default export */ __webpack_exports__["default"] = ({
    extends: app_pages_modules_view__WEBPACK_IMPORTED_MODULE_0__["default"],
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
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
// Vue configs...



const VueConfig = {
    devtools: true,
}

const VueOptions = {
    delimiters: ['<:', ':>'],
}

// merge vue options and configs
for (let property in VueConfig) {
    if (VueConfig.hasOwnProperty(property)) {
        vue__WEBPACK_IMPORTED_MODULE_0___default.a.config[property] = VueConfig[property];
    }
}

for (let property in VueOptions) {
    if (VueOptions.hasOwnProperty(property)) {
        vue__WEBPACK_IMPORTED_MODULE_0___default.a.options[property] = VueOptions[property];
    }
}

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
        height: 200, // 12.5em (16px)
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
        height: 200, // 12.5em (16px)
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
        height: 200, // 12.5em (16px)
    },
};


/***/ }),

/***/ "./src/Template/Layout/js/libs/bedita.js":
/*!***********************************************!*\
  !*** ./src/Template/Layout/js/libs/bedita.js ***!
  \***********************************************/
/*! exports provided: BELoader */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "BELoader", function() { return BELoader; });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);



/**
 * BEdita Helper Object
 */
const BELoader = {

    /**
     * load Beplugins' Vue components (global)
     *
     * @return {void}
     */
    loadBeditaPlugins() {
        const plugins = BEDITA.plugins;

        plugins.forEach(element => {
            const vueComponent = window[element] || global[element];
            if (vueComponent) {
                const BEPlugins = vueComponent.default;

                Object.keys(BEPlugins).forEach(componentName => {
                    if (typeof BEPlugins[componentName] === 'object') {
                        vue__WEBPACK_IMPORTED_MODULE_0___default.a.component(componentName, BEPlugins[componentName]);

                        console.debug(
                            `%c[${componentName}]%c component succesfully registred from %c${element}%c Plugin`,
                            'color: blue',
                            'color: black',
                            'color: red',
                            'color: black'
                        );
                    }
                });
            }
        });
    }
}




/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/Template/Layout/js/libs/filters.js":
/*!************************************************!*\
  !*** ./src/Template/Layout/js/libs/filters.js ***!
  \************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Converts a snake case string to title case.
 * Example: snake_case => Snake Case
 *
 * @param  {String} str the string to convert
 * @return {String}
 */
vue__WEBPACK_IMPORTED_MODULE_0___default.a.filter('humanize', function (str) {
    return str.split('_').map(function (item) {
        return item.charAt(0).toUpperCase() + item.substring(1);
    }).join(' ');
});


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

module.exports = __webpack_require__(/*! /var/www/ws/misc-projects/bedita4-web/src/Template/Layout/js/app/app.js */"./src/Template/Layout/js/app/app.js");


/***/ })

},[[0,"manifest","vendors"]]]);
//# sourceMappingURL=app.bundle.js.map