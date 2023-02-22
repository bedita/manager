import Vue from 'vue';

import 'libs/filters';
import 'config/config';

import '../../style.scss';

import { BELoader } from 'libs/bedita';

import { PanelView, PanelEvents } from 'app/components/panel-view';
import { confirm, error, info, prompt, warning } from 'app/components/dialog/dialog';

import datepicker from 'app/directives/datepicker';
import email from 'app/directives/email';
import jsoneditor from 'app/directives/jsoneditor';
import richeditor from 'app/directives/richeditor';
import uri from 'app/directives/uri';
import viewHelper from 'app/helpers/view';
import autoTranslation from 'app/helpers/api-translation';
import Autocomplete from '@trevoreyre/autocomplete-vue';

import merge from 'deepmerge';
import { t } from 'ttag';
import { buildSearchParams } from '../libs/urlUtils.js';

import vTitle from 'vuejs-title';

/* icons */
import { CarbonIconsVue } from '@carbon/icons-vue';
import { Add as IconAdd } from '@carbon/icons-vue/es/add/16.js';
import { Categories as IconCategories } from '@carbon/icons-vue/es/categories/16.js';
import { ChartRelationship as IconChartRelationship } from '@carbon/icons-vue/es/chart--relationship/16.js';
import { Checkmark as IconCheckmark } from '@carbon/icons-vue/es/checkmark/16.js';
import { ChevronLeft as IconChevronLeft } from '@carbon/icons-vue/es/chevron--left/20.js';
import { ChevronRight as IconChevronRight } from '@carbon/icons-vue/es/chevron--right/20.js';
import { Close as IconClose } from '@carbon/icons-vue/es/close/16.js';
import { Concept as IconConcept } from '@carbon/icons-vue/es/concept/32.js';
import { Copy as IconCopy } from '@carbon/icons-vue/es/copy/16.js';
import { Cube as IconCube } from '@carbon/icons-vue/es/db2--database/20.js';
import { Document as IconDocument } from '@carbon/icons-vue/es/document/16.js';
import { Download as IconDownload } from '@carbon/icons-vue/es/download/32.js';
import { Edit as IconEdit } from '@carbon/icons-vue/es/edit/16.js';
import { Error as IconError } from '@carbon/icons-vue/es/error/16.js';
import { Export as IconExport } from '@carbon/icons-vue/es/export/16.js';
import { Filter as IconFilter } from '@carbon/icons-vue/es/filter/16.js';
import { FilterEdit as IconFilterEdit } from '@carbon/icons-vue/es/filter--edit/16.js';
import { FilterReset as IconFilterReset} from '@carbon/icons-vue/es/filter--reset/16.js';
import { Grid as IconGrid16 } from '@carbon/icons-vue/es/grid/16.js';
import { Grid as IconGrid32 } from '@carbon/icons-vue/es/grid/32.js';
import { Help as IconHelp } from '@carbon/icons-vue/es/help/16.js';
import { Hourglass as IconHourglass } from '@carbon/icons-vue/es/hourglass/16.js'
import { Information as IconInformation } from '@carbon/icons-vue/es/information/16.js';
import { Launch as IconLaunch } from '@carbon/icons-vue/es/launch/16.js';
import { List as IconList } from '@carbon/icons-vue/es/list/16.js';
import { ListBulleted as IconListBulleted16 } from '@carbon/icons-vue/es/list--bulleted/16.js';
import { Locked as IconLocked } from '@carbon/icons-vue/es/locked/16.js';
import { Login as IconLogin } from '@carbon/icons-vue/es/login/16.js';
import { Logout as IconLogout } from '@carbon/icons-vue/es/logout/16.js';
import { Redo as IconRedo } from '@carbon/icons-vue/es/redo/16.js';
import { Replicate as IconReplicate } from '@carbon/icons-vue/es/replicate/16.js';
import { Save as IconSave } from '@carbon/icons-vue/es/save/16.js';
import { Search as IconSearch } from '@carbon/icons-vue/es/search/16.js';
import { Settings as IconSettings } from '@carbon/icons-vue/es/settings/32.js';
import { Stop as IconStop } from '@carbon/icons-vue/es/stop/16.js';
import { Subtract as IconSubtract } from '@carbon/icons-vue/es/subtract/16.js';
import { Switcher as IconSwitcher } from '@carbon/icons-vue/es/switcher/16.js';
import { Tag as IconTag } from '@carbon/icons-vue/es/tag/16.js';
import { TrashCan as IconTrashCan16 } from '@carbon/icons-vue/es/trash-can/16.js';
import { TrashCan as IconTrashCan20 } from '@carbon/icons-vue/es/trash-can/20.js';
import { TrashCan as IconTrashCan32 } from '@carbon/icons-vue/es/trash-can/32.js';
import { TreeView as IconTreeView } from '@carbon/icons-vue/es/tree-view/32.js';
import { Undo as IconUndo } from '@carbon/icons-vue/es/undo/16.js';
import { Unlink as IconUnlink } from '@carbon/icons-vue/es/unlink/16.js';
import { Unlocked as IconUnlocked } from '@carbon/icons-vue/es/unlocked/16.js';
import { Upload as IconUpload } from '@carbon/icons-vue/es/upload/16.js';
import { UserAdmin as IconUserAdmin } from '@carbon/icons-vue/es/user--admin/32.js';
import { UserProfile as IconUserProfile } from '@carbon/icons-vue/es/user--profile/32.js';
import { View as IconView } from '@carbon/icons-vue/es/view/16.js';
import { ViewFilled as IconViewFilled } from '@carbon/icons-vue/es/view--filled/16.js';
import { ViewOffFilled as IconViewOffFilled } from '@carbon/icons-vue/es/view--off--filled/16.js';
import { Wikis as IconWikis16 } from '@carbon/icons-vue/es/wikis/16.js';
import { Wikis as IconWikis32 } from '@carbon/icons-vue/es/wikis/32.js';

const _vueInstance = new Vue({
    el: 'main',

    components: {
        PanelView,
        Autocomplete,
        LoginPassword: () => import(/* webpackChunkName: "login-password" */'app/components/login-password/login-password'),
        Category: () => import(/* webpackChunkName: "category" */'app/components/category/category'),
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */'app/components/category-picker/category-picker'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        TagForm: () => import(/* webpackChunkName: "tag-form" */'app/components/tag-form/tag-form'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
        Dashboard: () => import(/* webpackChunkName: "modules-index" */'app/pages/dashboard/index'),
        DateRangesView: () => import(/* webpackChunkName: "date-ranges-view" */'app/components/date-ranges-view/date-ranges-view'),
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        IndexCell: () => import(/* webpackChunkName: "index-cell" */'app/components/index-cell/index-cell'),
        ModulesIndex: () => import(/* webpackChunkName: "modules-index" */'app/pages/modules/index'),
        ModulesView: () => import(/* webpackChunkName: "modules-view" */'app/pages/modules/view'),
        ObjectNav: () => import(/* webpackChunkName: "object-nav" */'app/components/object-nav/object-nav'),
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
        ObjectTypesList: () => import(/* webpackChunkName: "object-types-list" */'app/components/object-types-list/object-types-list'),
        TrashIndex: () => import(/* webpackChunkName: "trash-index" */'app/pages/trash/index'),
        TrashView: () => import(/* webpackChunkName: "trash-view" */'app/pages/trash/view'),
        ImportView: () => import(/* webpackChunkName: "import-index" */'app/pages/import/index'),
        ModelIndex: () => import(/* webpackChunkName: "model-index" */'app/pages/model/index'),
        AdminIndex: () => import(/* webpackChunkName: "admin-index" */'app/pages/admin/index'),
        AdminAppearance: () => import(/* webpackChunkName: "admin-appearance" */'app/pages/admin/appearance'),
        RelationsAdd: () => import(/* webpackChunkName: "relations-add" */'app/components/relation-view/relations-add'),
        EditRelationParams: () => import(/* webpackChunkName: "edit-relation-params" */'app/components/edit-relation-params'),
        HistoryInfo: () => import(/* webpackChunkName: "history-info" */'app/components/history/history-info'),
        FilterBoxView: () => import(/* webpackChunkName: "filter-box-view" */'app/components/filter-box'),
        FilterTypeView: () => import(/* webpackChunkName: "filter-type-view" */'app/components/filter-type'),
        MainMenu: () => import(/* webpackChunkName: "menu" */'app/components/menu'),
        FlashMessage: () => import(/* webpackChunkName: "flash-message" */'app/components/flash-message'),
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */'app/components/coordinates-view'),
        Secret: () => import(/* webpackChunkName: "secret" */'app/components/secret/secret'),
        // icons
        IconAdd: () => import(/* webpackChunkName: "icon-add" */'@carbon/icons-vue/es/add/16.js'),
        IconCategories: () => import(/* webpackChunkName: "icon-categories" */'@carbon/icons-vue/es/categories/16.js'),
        IconChartRelationship: () => import(/* webpackChunkName: "icon-chart-relationship" */'@carbon/icons-vue/es/chart--relationship/16.js'),
        IconCheckmark: () => import(/* webpackChunkName: "icon-checkmark" */'@carbon/icons-vue/es/checkmark/16.js'),
        IconChevronLeft: () => import(/* webpackChunkName: "icon-chevron-left" */'@carbon/icons-vue/es/chevron--left/32.js'),
        IconChevronRight: () => import(/* webpackChunkName: "icon-chevron-right" */'@carbon/icons-vue/es/chevron--right/32.js'),
        IconClose: () => import(/* webpackChunkName: "icon-close" */'@carbon/icons-vue/es/close/16.js'),
        IconConcept: () => import(/* webpackChunkName: "icon-concept" */'@carbon/icons-vue/es/concept/32.js'),
        IconCopy: () => import(/* webpackChunkName: "icon-copy" */'@carbon/icons-vue/es/copy/16.js'),
        IconCube: () => import(/* webpackChunkName: "icon-cube" */'@carbon/icons-vue/es/cube/20.js'),
        IconDocument: () => import(/* webpackChunkName: "icon-document" */'@carbon/icons-vue/es/document/16.js'),
        IconDownload: () => import(/* webpackChunkName: "icon-download" */'@carbon/icons-vue/es/download/32.js'),
        IconEdit: () => import(/* webpackChunkName: "icon-edit" */'@carbon/icons-vue/es/edit/16.js'),
        IconError: () => import(/* webpackChunkName: "icon-error" */'@carbon/icons-vue/es/error/16.js'),
        IconExport: () => import(/* webpackChunkName: "icon-export" */'@carbon/icons-vue/es/export/16.js'),
        IconFilter: () => import(/* webpackChunkName: "icon-filter" */'@carbon/icons-vue/es/filter/16.js'),
        IconFilterEdit: () => import(/* webpackChunkName: "icon-filter-edit" */'@carbon/icons-vue/es/filter--edit/16.js'),
        IconFilterReset: () => import(/* webpackChunkName: "icon-filter-reset" */'@carbon/icons-vue/es/filter--reset/16.js'),
        IconGrid16: () => import(/* webpackChunkName: "icon-grid-16" */'@carbon/icons-vue/es/grid/16.js'),
        IconGrid32: () => import(/* webpackChunkName: "icon-grid-32" */'@carbon/icons-vue/es/grid/32.js'),
        IconHelp: () => import(/* webpackChunkName: "icon-help" */'@carbon/icons-vue/es/help/16.js'),
        IconHourglass: () => import(/* webpackChunkName: "icon-hourglass" */'@carbon/icons-vue/es/hourglass/16.js'),
        IconInformation: () => import(/* webpackChunkName: "icon-information" */'@carbon/icons-vue/es/information/16.js'),
        IconLaunch: () => import(/* webpackChunkName: "icon-launch" */'@carbon/icons-vue/es/launch/16.js'),
        IconList: () => import(/* webpackChunkName: "icon-list" */'@carbon/icons-vue/es/list/16.js'),
        IconListBulleted16: () => import(/* webpackChunkName: "icon-list-bulleted-16" */'@carbon/icons-vue/es/list--bulleted/16.js'),
        IconLocked: () => import(/* webpackChunkName: "icon-locked" */'@carbon/icons-vue/es/locked/16.js'),
        IconLogin: () => import(/* webpackChunkName: "icon-login" */'@carbon/icons-vue/es/login/16.js'),
        IconLogout: () => import(/* webpackChunkName: "icon-logout" */'@carbon/icons-vue/es/logout/16.js'),
        IconRedo: () => import(/* webpackChunkName: "icon-redo" */'@carbon/icons-vue/es/redo/16.js'),
        IconReplicate: () => import(/* webpackChunkName: "icon-replicate" */'@carbon/icons-vue/es/replicate/16.js'),
        IconSave: () => import(/* webpackChunkName: "icon-save" */'@carbon/icons-vue/es/save/16.js'),
        IconSearch: () => import(/* webpackChunkName: "icon-search" */'@carbon/icons-vue/es/search/16.js'),
        IconSettings: () => import(/* webpackChunkName: "icon-settings" */'@carbon/icons-vue/es/settings/32.js'),
        IconStop: () => import(/* webpackChunkName: "icon-stop" */'@carbon/icons-vue/es/stop/16.js'),
        IconSubtract: () => import(/* webpackChunkName: "icon-subtract" */'@carbon/icons-vue/es/subtract/16.js'),
        IconSwitcher: () => import(/* webpackChunkName: "icon-switcher" */'@carbon/icons-vue/es/switcher/16.js'),
        IconTag: () => import(/* webpackChunkName: "icon-tag" */'@carbon/icons-vue/es/tag/16.js'),
        IconTrashCan16: () => import(/* webpackChunkName: "icon-trash-can-16" */'@carbon/icons-vue/es/trash-can/16.js'),
        IconTrashCan20: () => import(/* webpackChunkName: "icon-trash-can-20" */'@carbon/icons-vue/es/trash-can/20.js'),
        IconTrashCan32: () => import(/* webpackChunkName: "icon-trash-can-32" */'@carbon/icons-vue/es/trash-can/32.js'),
        IconTreeView: () => import(/* webpackChunkName: "icon-tree-view" */'@carbon/icons-vue/es/tree-view/32.js'),
        IconUndo: () => import(/* webpackChunkName: "icon-undo" */'@carbon/icons-vue/es/undo/16.js'),
        IconUnlink: () => import(/* webpackChunkName: "icon-unlink" */'@carbon/icons-vue/es/unlink/16.js'),
        IconUnlocked: () => import(/* webpackChunkName: "icon-unlocked" */'@carbon/icons-vue/es/unlocked/16.js'),
        IconUpload: () => import(/* webpackChunkName: "icon-upload" */'@carbon/icons-vue/es/upload/16.js'),
        IconUserAdmin: () => import(/* webpackChunkName: "icon-user-admin" */'@carbon/icons-vue/es/user--admin/32.js'),
        IconUserProfile: () => import(/* webpackChunkName: "icon-user-profile" */'@carbon/icons-vue/es/user--profile/32.js'),
        IconView: () => import(/* webpackChunkName: "icon-view" */'@carbon/icons-vue/es/view/16.js'),
        IconViewFilled: () => import(/* webpackChunkName: "icon-view-filled" */'@carbon/icons-vue/es/view--filled/16.js'),
        IconViewOffFilled: () => import(/* webpackChunkName: "icon-view-off-filled" */'@carbon/icons-vue/es/view--off--filled/16.js'),
        IconWikis16: () => import(/* webpackChunkName: "icon-wikis-16" */'@carbon/icons-vue/es/wikis/16.js'),
        IconWikis32: () => import(/* webpackChunkName: "icon-wikis-32" */'@carbon/icons-vue/es/wikis/32.js'),
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
            getCSFRToken: () => BEDITA.csrfToken,
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
        Vue.use(email);
        Vue.use(uri);

        // Register helpers
        Vue.use(viewHelper);
        Vue.use(autoTranslation);
        Vue.use(Autocomplete);

        Vue.use(vTitle);

        // icons
        Vue.use(CarbonIconsVue, {
            components: {
                IconAdd,
                IconCategories,
                IconChartRelationship,
                IconCheckmark,
                IconChevronLeft,
                IconChevronRight,
                IconClose,
                IconConcept,
                IconCopy,
                IconCube,
                IconDocument,
                IconDownload,
                IconEdit,
                IconError,
                IconExport,
                IconFilter,
                IconFilterEdit,
                IconFilterReset,
                IconGrid16,
                IconGrid32,
                IconHelp,
                IconHourglass,
                IconInformation,
                IconLaunch,
                IconList,
                IconListBulleted16,
                IconLocked,
                IconLogin,
                IconLogout,
                IconRedo,
                IconReplicate,
                IconSave,
                IconSearch,
                IconSettings,
                IconStop,
                IconSubtract,
                IconSwitcher,
                IconTag,
                IconTrashCan16,
                IconTrashCan20,
                IconTrashCan32,
                IconTreeView,
                IconUndo,
                IconUnlink,
                IconUnlocked,
                IconUpload,
                IconUserAdmin,
                IconUserProfile,
                IconView,
                IconViewFilled,
                IconViewOffFilled,
                IconWikis16,
                IconWikis32,
            },
        });

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

        // listen events emitted on this vue instance
        this.$on('filter-update-page-size', this.onUpdatePageSize);
        this.$on('filter-update-current-page', this.onUpdateCurrentPage);

        Vue.prototype.$eventBus = new Vue();
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
            // register functions in BEDITA to make them reusable in plugins
            BEDITA.confirm = confirm;
            BEDITA.error = error;
            BEDITA.info = info;
            BEDITA.prompt = prompt;
            BEDITA.warning = warning;
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
            const confirmCallback = (cloneTitle, cloneRelations, dialog) => {
                const query = `?title=${cloneTitle || defaultTitle}&cloneRelations=${cloneRelations || false}`;
                const origin = window.location.origin;
                const path = window.location.pathname.replace('/view/', '/clone/');
                const url = `${origin}${path}${query}`;
                const newTab = window.open(url, '_blank');
                newTab.focus();
                dialog.hide(true);
            };
            const options = { checkLabel: t`Clone relations`, checkValue: false };

            prompt(msg, defaultTitle, confirmCallback, document.body, options);
        },

        /**
         * listen to FilterBoxView event filter-objects
         *
         * @param {Object} query
         *
         * @return {void}
         */
        onFilterObjects(query) {
            // remove from query string filter `history_editor` if false
            if (!query.filter.history_editor) {
                delete query.filter.history_editor;
            }

            this.urlFilterQuery = query;
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
                while ((matches = filterExp.exec(urlParams))) {
                    if (matches && matches.length === 3) {
                        const filterGroup = matches[1]; // keys group (ex. [status], [modified][lte])
                        const filterValue = matches[2]; // param value

                        let paramKeys = []
                        let keysMatches = [];

                        // extract keys from keys group and put it in paramKeys
                        while ((keysMatches = keysExp.exec(filterGroup))) {
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
        buildUrlWithParams(params) {
            const url = new URL(`${window.location.origin}${window.location.pathname}`);
            url.search = buildSearchParams(params, url.searchParams).toString();

            return url.href;
        },

        /**
         * reset queryString in search keeping pagination options
         *
         * @returns {void}
         */
        resetFilters() {
            window.location.replace(this.buildUrlWithParams({ reset: 1 }));
        },

        /**
         * apply page filters such as query string or pagination
         *
         * @params {Object} filters filters object
         *
         * @returns {void}
         */
        applyFilters(filters) {
            if (filters?.filter?.type) {
                delete filters.filter.type;
            }
            if (this.selectedTypes.length > 0) {
                const typesFilter = {type: this.selectedTypes};
                filters.filter = {...filters.filter, ...typesFilter};
            }
            const url = this.buildUrlWithParams({
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
         * alerts onbeforeunload if forms changed and it's not a submit
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
            * Listen for submit: if action is "delete" it shows warning dialog
            */
            this.$el.addEventListener('submit', (ev) => {
                ev.preventDefault();
                const form = ev.target;
                if (!form) {
                    return;
                }

                const trashActions = [
                    '/trash/delete',
                    '/trash/empty',
                ];

                let msg = '';
                let done = false;
                for (const action of trashActions) {
                    if (!done && form.action.includes(action)) {
                        msg = t`If you confirm, this data will be gone forever. Are you sure?`;
                        done = true;
                    }
                }
                if (!done) {
                    const hardDeleteActions = [
                        '/delete',
                        '/model/object_types/remove',
                        '/model/relations/remove',
                        '/model/tags/remove',
                        '/model/categories/remove',
                    ];
                    for (const action of hardDeleteActions) {
                        if (!done && form.action.includes(action)) {
                            if (action === '/delete') {
                                msg = t`Do you really want to trash the object?`;
                            } else {
                                msg = t`If you confirm, this resource will be gone forever. Are you sure?`;
                            }
                            done = true;
                        }
                    }
                }

                _vueInstance.dataChanged.clear();
                if (!msg) {
                    return form.submit();
                }

                confirm(msg, t`yes, proceed`, form.submit.bind(form));
            });

            /**
            * Ask confirmation before leaving the page if there are unsaved changes
            */
            window.onbeforeunload = () => {
                for (const [key, value] of _vueInstance.dataChanged) {
                    if (value.changed) {
                        return t`There are unsaved changes, are you sure you want to leave page?`;
                    }
                    console.debug(key);
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
