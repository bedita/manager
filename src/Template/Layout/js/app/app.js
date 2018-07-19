import Vue from 'vue';

import 'libs/filters';
import 'config/config';

import 'Template/Layout/style.scss';

import { BELoader } from 'libs/bedita';

import ModulesIndex from 'app/pages/modules/index';
import ModulesView from 'app/pages/modules/view';
import TrashIndex from 'app/pages/trash/index';
import TrashView from 'app/pages/trash/view';
import RelationsAdd from 'app/components/relation-view/relations-add';

import datepicker from 'app/directives/datepicker';
import jsoneditor from 'app/directives/jsoneditor';
import richeditor from 'app/directives/richeditor';
import VueHotkey from 'v-hotkey';

import sleep from 'sleep-promise';

const _vueInstance = new Vue({
    el: 'main',

    components: {
        ModulesIndex,
        ModulesView,
        TrashIndex,
        TrashView,
        RelationsAdd,
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
        Vue.use(jsoneditor);
        Vue.use(datepicker);
        Vue.use(richeditor);
        Vue.use(VueHotkey);

        // load BEplugins's components
        BELoader.loadBeditaPlugins();
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
    },

    methods: {
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
            if(data.relationName){
                this.$refs["moduleView"]
                    .$refs[data.relationName]
                    .$refs["relation"].appendRelations(data.objects);
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
