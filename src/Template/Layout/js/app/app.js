import Vue from 'vue';

import 'libs/filters';
import 'config/config';

import 'Template/Layout/style.scss';

import { BELoader } from 'libs/bedita';

import { PanelView, PanelEvents } from 'app/components/panel-view';
import { BeditaDialog } from './components/bedita-dialog/bedita-dialog';

import datepicker from 'app/directives/datepicker';
import jsoneditor from 'app/directives/jsoneditor';
import richeditor from 'app/directives/richeditor';
import viewHelper from 'app/helpers/view';
import autoTranslation from 'app/helpers/api-translation';
import Autocomplete from '@trevoreyre/autocomplete-vue';

import merge from 'deepmerge';
import { t } from 'ttag';

const _vueInstance = new Vue({
    el: 'main',

    components: {
        PanelView,
        Autocomplete,
        Dashboard: () => import(/* webpackChunkName: "modules-index" */'app/pages/dashboard/index'),
        DateRangesView: () => import(/* webpackChunkName: "date-ranges-view" */'app/components/date-ranges-view/date-ranges-view'),
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        ModulesIndex: () => import(/* webpackChunkName: "modules-index" */'app/pages/modules/index'),
        ModulesView: () => import(/* webpackChunkName: "modules-view" */'app/pages/modules/view'),
        TrashIndex: () => import(/* webpackChunkName: "trash-index" */'app/pages/trash/index'),
        TrashView: () => import(/* webpackChunkName: "trash-view" */'app/pages/trash/view'),
        ImportView: () => import(/* webpackChunkName: "import-index" */'app/pages/import/index'),
        ModelIndex: () => import(/* webpackChunkName: "model-index" */'app/pages/model/index'),
        RelationsAdd: () => import(/* webpackChunkName: "relations-add" */'app/components/relation-view/relations-add'),
        EditRelationParams: () => import(/* webpackChunkName: "edit-relation-params" */'app/components/edit-relation-params'),
        HistoryInfo: () => import(/* webpackChunkName: "history-info" */'app/components/history/history-info'),
        FilterBoxView: () => import(/* webpackChunkName: "filter-box-view" */'app/components/filter-box'),
        FilterTypeView: () => import(/* webpackChunkName: "filter-type-view" */'app/components/filter-type'),
        MainMenu: () => import(/* webpackChunkName: "menu" */'app/components/menu'),
        FlashMessage: () => import(/* webpackChunkName: "flash-message" */'app/components/flash-message'),
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */'app/components/coordinates-view'),
        BeditaDialog,
    },

    data() {
        return {
            vueLoaded: false,
            urlPagination: '',
            searchQuery: '',
            pageSize: '',
            page: '',
            sort: '',
            dataChanged: false,

            // filters
            urlFilterQuery: {
                q: '',
                filter: {},
            },
            pagination: {
                page: '',
                page_size: '',
            },
            selectedTypes: []
        }
    },

    /**
    * properties or methods available for injection into its descendants
    * (inject: ['property'])
    */
    provide() {
        return {
            getCSFRToken: (...args) => BEDITA.csrfToken,
        }
    },

    /**
     * setup Vue instance before creation
     *
     * @return {void}
     */
    beforeCreate() {
        // Register directives
        Vue.use(jsoneditor);
        Vue.use(datepicker);
        Vue.use(richeditor);

        // Register helpers
        Vue.use(viewHelper);
        Vue.use(autoTranslation);
        Vue.use(Autocomplete);

        // load BEplugins's components
        BELoader.loadBeditaPlugins();
    },

    created() {
        this.vueLoaded = true;
        this.dataChanged = new Map();

        // load url params when component initialized
        this.loadUrlParams();

        const rootEl = document.querySelector('html');
        const bodyEl = document.querySelector('body');
        PanelEvents.listen('panel:requested', null, () => {
            bodyEl.classList.add('panel-is-open');
            rootEl.classList.add('is-clipped');
        });
        PanelEvents.listen('panel:closed', null, () => {
            bodyEl.classList.remove('panel-is-open');
            rootEl.classList.remove('is-clipped');
        });
    },

    watch: {
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
            this.alertBeforePageUnload(BEDITA.template);
        });
    },

    methods: {

        /**
         * Close panel
         *
         * @return {void}
         */
        closePanel() {
            PanelEvents.closePanel();
        },

        /**
         * Clone object
         * Prompt for title change
         *
         * @return {void}
         */
        clone() {
            const title = document.getElementById('title').value || t('Untitled');
            const msg = t`Please insert a new title on "${title}" clone`;
            const defaultTitle = title + '-' + t`copy`;
            let dialog = this.$root.$refs.beditaDialog;

            const confirmCallback = (cloneTitle = defaultTitle) => {
                const query = `?title=${cloneTitle}`;
                const origin = window.location.origin;
                const path = window.location.pathname.replace('/view/', '/clone/');
                const url = `${origin}${path}${query}`;
                const newTab = window.open(url, '_blank');
                newTab.focus();
                dialog.hide();
            };

            dialog.prompt(msg, defaultTitle, confirmCallback);
        },

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
         * listen to FilterTypeView event filter-type-page
         */
        onUpdateQueryTypes(types) {
            this.selectedTypes = types;
        },

        /**
        * extract params from page url
        *
        * @returns {void}
        */
        loadUrlParams() {
            // look for query string params in window url
            if (window.location.search) {
                const urlParams = decodeURIComponent(window.location.search);

                // search for q='some string' both after ? and & tokens
                const queryStringExp = /[?&]q=([^&#]*)/g;
                let matches = urlParams.match(queryStringExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(queryStringExp, '$1'));
                    this.urlFilterQuery.q = matches[0];
                }

                // search for filter['filter_name']['modifier']='filter_value' both after ? and & tokens
                const filterExp = /[?&]filter(\[.*?\])=([^&#]*)/g; // extract properties group and value
                const keysExp = /\[(.*?)\]/g; // extract single property from the properties group

                let filter = {};
                while (matches = filterExp.exec(urlParams)) {
                    if (matches && matches.length === 3) {
                        const filterGroup = matches[1]; // keys group (ex. [status], [modified][lte])
                        const filterValue = matches[2]; // param value

                        let paramKeys = []
                        let keysMatches = [];

                        // extract keys from keys group and put it in paramKeys
                        while (keysMatches = keysExp.exec(filterGroup)) {
                            paramKeys.push(keysMatches[1]);
                        }

                        // create object with keys (many sublevels): first invert array of keys, then wrap around each key a
                        // new object with new { key: previousObject } creating the right hierarchy
                        const obj = paramKeys.reverse().reduce((accumulator, keyName) => {
                            let param = {}
                            param[keyName] = accumulator;
                            return param;
                        }, filterValue);

                        filter = merge(filter, obj);
                    }
                }
                this.urlFilterQuery.filter = filter;

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
         * - q=_string_
         * - filter[_name_]=_value_
         * - page_size=_value_
         *
         * @param {Object} params
         * @returns {String} url
         */
        buildUrlParams(params) {
            let url = `${window.location.origin}${window.location.pathname}`;
            const queryId = '?';
            const separator = '&';
            const paramsKeys = Object.keys(params);

            if (paramsKeys && paramsKeys.length) {
                let fields = [];

                paramsKeys.forEach((key) =>  {
                    if (params[key]) {
                        const query = params[key];

                        // parse filter property
                        if (key === 'filter') {
                            Object.keys(query).forEach((filterKey) => {
                                if (typeof query[filterKey] === 'object') {
                                    const filter = query[filterKey];
                                    Object.keys(filter).forEach((modifier) => {
                                        if (filter[modifier] !== '') {
                                            // look up for param modifier (i.e dates)
                                            const encoded = encodeURIComponent(filter[modifier]);
                                            fields.push(`filter[${filterKey}][${modifier}]=${encoded}`);
                                        }
                                    });
                                } else if (query[filterKey] !== '') {
                                    const encoded = encodeURIComponent(query[filterKey]);
                                    fields.push(`filter[${filterKey}]=${encoded}`);
                                }
                            });
                        } else {
                            const encoded = encodeURIComponent(query);
                            fields.push(`${key}=${encoded}`);
                        }
                    }
                });
                url += fields.length ? queryId : '';
                url += fields.join(separator);
            }

            return url;
        },

        /**
         * reset queryString in search keeping pagination options
         *
         * @returns {void}
         */
        resetFilters() {
            window.location.replace(this.buildUrlParams({ reset: 1 }));
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
                filter: filters.filter,
                page_size: this.pageSize,
                page: this.page,
                sort: this.sort,
                _search: 1
            });
            window.location.replace(url);
        },

        /**
         * alerts onbeforeunload if forms changed and it's not a submit.
         *
         * @returns {void}
         */
        alertBeforePageUnload() {
            /*
                Listen for focusin: "normal" HTML element need to store original value in order to make a diff with new values
            */
            this.$el.addEventListener('focusin', (ev) => {
                const element = ev.target;
                const form = element && element.form;

                // forms need to have attribute check-changes set to true to enable changes check
                const checkChanges = form && form.getAttribute('check-changes') === 'true';
                if (checkChanges) {
                    if (typeof element.dataset.originalValue === 'undefined') {
                        if (element.nodeName === 'INPUT') {
                            // storing original value for the element
                            if (element.type === 'radio') {
                                const name = element.name;
                                if (name) {
                                    const group = document.querySelectorAll(`input[name='${name}']`);
                                    const checked = document.querySelector(`input[name='${name}']:checked`);
                                    if (checked) {
                                        group.forEach(el => el.dataset.originalValue = checked.value);
                                    }
                                }
                            } else if (element.type === 'checkbox') {
                                const name = element.name;
                                if (name) {
                                    const checked = document.querySelectorAll(`input[name='${name}']:checked`);
                                    if (checked) {
                                        element.dataset.originalValue = JSON.stringify(checked);
                                    }
                                }
                            } else {
                                element.dataset.originalValue = element.value;
                            }
                        } else if (element.nodeName === 'TEXTAREA') {
                            // storing original value for the element
                            element.dataset.originalValue = element.value;
                        }
                    }
                }
            }, true);

            /*
                Listen for change: Handles change events and checks if form/page has been modified
            */
            this.$el.addEventListener('change', (ev) => {
                const sender = ev.detail;
                if (sender && sender.id) {
                    this.dataChanged.set(sender.id, {
                        changed: sender.isChanged
                    });
                } else {
                    // support for normal change Events trying to figure out a unique id
                    const element = ev.target;
                    const form = element && element.form;
                    const checkChanges = form && form.getAttribute('check-changes') === 'true';
                    if (checkChanges && element.name) {
                        const name = element.name;
                        const formId = element.form.getAttribute('id');
                        const elementId = element.id;
                        const originalValue = element.dataset.originalValue;
                        let value = element.value;
                        let id = `${formId}#${elementId}`;

                        if (element.type === 'radio' || element.type === 'checkbox') {
                            id = `${formId}#${name}`;
                        }

                        if (!id) {
                            // if I can't make an id out of this, don't bother
                            return true;
                        }

                        if (element.type === 'checkbox') {
                            const checked = document.querySelectorAll(`input[name='${name}']:checked`);
                            if (checked) {
                                value = JSON.stringify(checked);
                            }
                        }

                        this.dataChanged.set(id, {
                            changed: value !== originalValue,
                        });
                    }
                }
            });

            /**
            * Listen for submit: if action is /delete it shows warning dialog
            */
            this.$el.addEventListener('submit', (ev) => {
                ev.preventDefault();
                const form = ev.target;
                if (!form) {
                    return;
                }

                let msg = '';
                if (form.action.endsWith('/trash/delete') || form.action.endsWith('/trash/empty')) {
                    msg = t`If you confirm, this data will be gone forever. Are you sure?`;
                } else if (form.action.endsWith('/delete')) {
                    msg = t`Do you really want to trash the object?`;
                }

                _vueInstance.dataChanged.clear();
                if (!msg) {
                    return form.submit();
                }

                let dialog = this.$root.$refs.beditaDialog;
                dialog.confirm(msg, t`yes, proceed`, form.submit.bind(form));
            });

            /**
            * Ask confirmation before leaving the page if there are unsaved changes
            */
            window.onbeforeunload = () => {
                for (const [key, value] of _vueInstance.dataChanged) {
                    if (value.changed) {
                        return t`There are unsaved changes, are you sure you want to leave page?`;
                    }
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
