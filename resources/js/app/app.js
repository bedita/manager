import Vue from 'vue';

import 'config/config';

import '../../style.scss';

import { BELoader } from 'libs/bedita';

import { EventBus } from 'app/components/event-bus';
import { PanelView, PanelEvents } from 'app/components/panel-view';
import { confirm, error, info, success, prompt, warning } from 'app/components/dialog/dialog';

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

import { Icon as AppIcon } from '@iconify/vue2';

const _vueInstance = new Vue({
    el: 'main',

    components: {
        EventBus,
        PanelView,
        Autocomplete,
        LoginPassword: () => import(/* webpackChunkName: "login-password" */'app/components/login-password/login-password'),
        LabelsForm:() => import(/* webpackChunkName: "labels-form" */'app/components/labels-form'),
        CategoryForm: () => import(/* webpackChunkName: "category-form" */'app/components/category-form/category-form'),
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */'app/components/category-picker/category-picker'),
        ObjectTypesPicker: () => import(/* webpackChunkName: "object-types-picker" */'app/components/object-types-picker/object-types-picker'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        TagForm: () => import(/* webpackChunkName: "tag-form" */'app/components/tag-form/tag-form'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
        DashboardSearch: () => import(/* webpackChunkName: "dashboard-search" */'app/components/dashboard-search'),
        DateRange: () => import(/* webpackChunkName: "date-range" */'app/components/date-range/date-range'),
        DateRangesView: () => import(/* webpackChunkName: "date-ranges-view" */'app/components/date-ranges-view/date-ranges-view'),
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        TreeSlug: () => import(/* webpackChunkName: "tree-slug" */'app/components/tree-slug/tree-slug'),
        TreeCompact: () => import(/* webpackChunkName: "tree-compact" */'app/components/tree-compact/tree-compact'),
        IndexCell: () => import(/* webpackChunkName: "index-cell" */'app/components/index-cell/index-cell'),
        ModulesIndex: () => import(/* webpackChunkName: "modules-index" */'app/pages/modules/index'),
        ModulesView: () => import(/* webpackChunkName: "modules-view" */'app/pages/modules/view'),
        ObjectNav: () => import(/* webpackChunkName: "object-nav" */'app/components/object-nav/object-nav'),
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
        ObjectPropertyAdd: () => import(/* webpackChunkName: "object-property-add" */'app/components/object-property/object-property-add'),
        ObjectTypesList: () => import(/* webpackChunkName: "object-types-list" */'app/components/object-types-list/object-types-list'),
        TrashIndex: () => import(/* webpackChunkName: "trash-index" */'app/pages/trash/index'),
        TrashView: () => import(/* webpackChunkName: "trash-view" */'app/pages/trash/view'),
        ImportIndex: () => import(/* webpackChunkName: "import-index" */'app/pages/import/index'),
        ImportJobs: () => import(/* webpackChunkName: "import-jobs" */'app/components/import/jobs'),
        ImportResult: () => import(/* webpackChunkName: "import-result" */'app/components/import/result'),
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
        ModelIndex: () => import(/* webpackChunkName: "model-index" */'app/pages/model/index'),
        AdminIndex: () => import(/* webpackChunkName: "admin-index" */'app/pages/admin/index'),
        AdminAppearance: () => import(/* webpackChunkName: "admin-appearance" */'app/pages/admin/appearance'),
        AdminStatistics: () => import(/* webpackChunkName: "admin-statistics" */'app/pages/admin/statistics'),
        RelationsAdd: () => import(/* webpackChunkName: "relations-add" */'app/components/relation-view/relations-add'),
        EditChildrenParams: () => import(/* webpackChunkName: "edit-children-params" */'app/components/edit-children-params'),
        EditRelationParams: () => import(/* webpackChunkName: "edit-relation-params" */'app/components/edit-relation-params'),
        HistoryInfo: () => import(/* webpackChunkName: "history-info" */'app/components/history/history-info'),
        FilterBoxView: () => import(/* webpackChunkName: "filter-box-view" */'app/components/filter-box'),
        MainMenu: () => import(/* webpackChunkName: "menu" */'app/components/menu'),
        FlashMessage: () => import(/* webpackChunkName: "flash-message" */'app/components/flash-message'),
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */'app/components/coordinates-view'),
        Secret: () => import(/* webpackChunkName: "secret" */'app/components/secret/secret'),
        KeyValueList: () => import(/* webpackChunkName: "key-value-list" */'app/components/json-fields/key-value-list'),
        StringList: () => import(/* webpackChunkName: "string-list" */'app/components/json-fields/string-list'),
        Thumbnail:() => import(/* webpackChunkName: "thumbnail" */'app/components/thumbnail/thumbnail'),
        Permission:() => import(/* webpackChunkName: "permission" */'app/components/permission/permission'),
        Permissions:() => import(/* webpackChunkName: "permissions" */'app/components/permissions/permissions'),
        PaginationNavigation:() => import(/* webpackChunkName: "pagination-navigation" */'app/components/pagination-navigation/pagination-navigation'),
        ObjectsHistory:() => import(/* webpackChunkName: "objects-history" */'app/components/objects-history/objects-history'),
        RecentActivity:() => import(/* webpackChunkName: "recent-activity" */'app/components/recent-activity/recent-activity'),
        ShowHide:() => import(/* webpackChunkName: "show-hide" */'app/components/show-hide/show-hide'),
        SystemInfo:() => import(/* webpackChunkName: "system-info" */'app/components/system-info/system-info'),
        UserAccesses:() => import(/* webpackChunkName: "user-accesses" */'app/components/user-accesses/user-accesses'),
        LanguageSelector:() => import(/* webpackChunkName: "language-selector" */'app/components/language-selector/language-selector'),
        ClipboardItem: () => import(/* webpackChunkName: "clipboard-item" */'app/components/clipboard-item/clipboard-item'),
        ObjectCaptions: () => import(/* webpackChunkName: "object-captions" */'app/components/object-captions/object-captions'),
        ObjectCategories: () => import(/* webpackChunkName: "object-categories" */'app/components/object-categories/object-categories'),
        PlaceholderList: () => import(/* webpackChunkName: "placeholder-list" */'app/components/placeholder-list/placeholder-list'),
        BarChart:() => import(/* webpackChunkName: "bar-chart" */'app/components/charts/bar-chart'),
        SortRelated: () => import(/* webpackChunkName: "sort-related" */'app/components/sort-related/sort-related'),
        MediaItem: () => import(/* webpackChunkName: "media-item" */'app/components/media-item/media-item'),
        FastCreate: () => import(/* webpackChunkName: "fast-create" */'app/components/fast-create/fast-create'),
        FastCreateContainer: () => import(/* webpackChunkName: "fast-create-container" */'app/components/fast-create/fast-create-container'),
        FormField: () => import(/* webpackChunkName: "form-field" */'app/components/fast-create/form-field'),
        FileUpload: () => import(/* webpackChunkName: "file-upload" */'app/components/file-upload/file-upload'),
        FieldCheckbox: () => import(/* webpackChunkName: "field-checkbox" */'app/components/form/field-checkbox'),
        FieldDate: () => import(/* webpackChunkName: "field-date" */'app/components/form/field-date'),
        FieldGeoCoordinates: () => import(/* webpackChunkName: "field-geo-coordinates" */'app/components/form/field-geo-coordinates'),
        FieldInteger: () => import(/* webpackChunkName: "field-integer" */'app/components/form/field-integer'),
        FieldJson: () => import(/* webpackChunkName: "field-json" */'app/components/form/field-json'),
        FieldMultipleCheckboxes: () => import(/* webpackChunkName: "field-multiple-checkboxes" */'app/components/form/field-multiple-checkboxes'),
        FieldNumber: () => import(/* webpackChunkName: "field-number" */'app/components/form/field-number'),
        FieldPassword: () => import(/* webpackChunkName: "field-password" */'app/components/form/field-password'),
        FieldPlaintext: () => import(/* webpackChunkName: "field-plaintext" */'app/components/form/field-plaintext'),
        FieldRadio: () => import(/* webpackChunkName: "field-radio" */'app/components/form/field-radio'),
        FieldSelect: () => import(/* webpackChunkName: "field-select" */'app/components/form/field-select'),
        FieldString: () => import(/* webpackChunkName: "field-string" */'app/components/form/field-string'),
        FieldTextarea: () => import(/* webpackChunkName: "field-textarea" */'app/components/form/field-textarea'),
        FieldTitle: () => import(/* webpackChunkName: "field-title" */'app/components/form/field-title'),
        CalendarView: () => import(/* webpackChunkName: "calendar-view" */'app/components/calendar-view/calendar-view'),
        ComponentsPlayground: () => import(/* webpackChunkName: "components-playground" */'app/components/components-playground'),
        ObjectInfo: () => import(/* webpackChunkName: "object-info" */'app/components/object-info/object-info'),
        RelatedObjectsFilter: () => import(/* webpackChunkName: "related-objects-filter" */'app/components/related-objects-filter/related-objects-filter'),
        ModuleProperties: () => import(/* webpackChunkName: "module-properties" */'app/components/module/module-properties'),
        ModuleSetup: () => import(/* webpackChunkName: "module-setup" */'app/components/module/module-setup'),
        AddRelatedById: () => import(/* webpackChunkName: "add-related-by-id" */'app/components/add-related-by-id/add-related-by-id'),
        UploadedObject: () => import(/* webpackChunkName: "uploaded-object" */'app/components/uploaded-object/uploaded-object.vue'),
        RibbonItem: () => import(/* webpackChunkName: "ribbon-item" */'./components/ribbon-item/ribbon-item.vue'),
        MailPreview: () => import(/* webpackChunkName: "mail-preview" */'./components/mail-preview/mail-preview.vue'),
        ObjectAnnotations: () => import(/* webpackChunkName: "object-annotations" */'./components/object-annotations/object-annotations.vue'),
        AppIcon,
    },

    /**
    * properties or methods available for injection into its descendants
    * (inject: ['property'])
    */
    provide() {
        return {
            getCSFRToken: () => BEDITA.csrfToken,
        };
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
            selectedTypes: [],
        }
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

        Vue.use(vTitle, {
            bgColor: '#000000'
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
        this.$on('resource-changed', this.onResourceChanged);

        Vue.prototype.$eventBus = new Vue();
    },

    mounted: function () {
        this.$nextTick(function () {
            this.alertBeforePageUnload(BEDITA.template);
            // register functions in BEDITA to make them reusable in plugins
            BEDITA.confirm = confirm;
            BEDITA.error = error;
            BEDITA.info = info;
            BEDITA.success = success;
            BEDITA.prompt = prompt;
            BEDITA.warning = warning;
            this.selectedTypes = this.queryFilter?.filter?.type || [];
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

        clone(objectType) {
            const unique = BEDITA.cloneConfig[objectType]?.unique || [];
            const title = document.getElementById('title').value || t('Untitled');
            const msg = t`Please insert a new title on "${title}" clone`;
            const defaultTitle = title + '-' + t`copy`;
            const confirmCallback = (dialog, options) => {
                let query = `?title=${options?.title || defaultTitle}`;
                for (const uitem of options?.unique || []) {
                    query += `&${uitem.field}=${uitem.value}`;
                }
                query += `&relationships=${options?.relations || false}`;
                query += `&translations=${options?.translations || false}`;
                const origin = window.location.origin;
                const path = window.location.pathname.replace('/view/', '/clone/');
                const url = `${origin}${path}${query}`;
                const newTab = window.open(url, '_blank');
                newTab.focus();
                dialog.hide(true);
            };
            const uniqueOptions = [];
            for (const field of unique) {
                let label = document.querySelector(`label[for=${field}]`)?.innerText;
                label = label || this.$helpers.humanize(field);
                uniqueOptions.push({
                    field: field,
                    label: label,
                    value: document.getElementById(field).value + '-' + t`copy`,
                });
            }
            prompt(msg, defaultTitle, confirmCallback, document.body, {
                checks: [
                    { name: 'relations', label: t`Clone relations`, value: false },
                    { name: 'translations', label: t`Clone translations`, value: false }
                ],
                unique: uniqueOptions
            });
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
            const url = this.buildUrlWithParams({
                q: filters.q,
                filter: filters.filter,
                page_size: this.pageSize,
                page: this.page,
                sort: this.sort,
                _search: 1,
                view_type: 'list',
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
                    '/translation/delete',
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

        onResourceChanged() {
            if (!BEDITA.canSave) {
                return;
            }
            const moduleBox = document.getElementById('module-icon');
            if (moduleBox) {
                // Show floppy icon inside module box
                moduleBox.classList.add('resource-changed')
            }
        }
    }
});

window._vueInstance = _vueInstance;

// use component everywhere in Manager
Vue.component('AppIcon', AppIcon);
Vue.component('CalendarView', _vueInstance.$options.components.CalendarView);
Vue.component('ClipboardItem', _vueInstance.$options.components.ClipboardItem);
Vue.component('DateRangesView', _vueInstance.$options.components.DateRangesView);
Vue.component('FieldCheckbox', _vueInstance.$options.components.FieldCheckbox);
Vue.component('FieldGeoCoordinates', _vueInstance.$options.components.FieldGeoCoordinates);
Vue.component('FieldDate', _vueInstance.$options.components.FieldDate);
Vue.component('FieldInteger', _vueInstance.$options.components.FieldInteger);
Vue.component('FieldJson', _vueInstance.$options.components.FieldJson);
Vue.component('FieldMultipleCheckboxes', _vueInstance.$options.components.FieldMultipleCheckboxes);
Vue.component('FieldNumber', _vueInstance.$options.components.FieldNumber);
Vue.component('FieldPassword', _vueInstance.$options.components.FieldPassword);
Vue.component('FieldPlaintext', _vueInstance.$options.components.FieldPlaintext);
Vue.component('FieldRadio', _vueInstance.$options.components.FieldRadio);
Vue.component('FieldSelect', _vueInstance.$options.components.FieldSelect);
Vue.component('FieldString', _vueInstance.$options.components.FieldString);
Vue.component('FieldTextarea', _vueInstance.$options.components.FieldTextarea);
Vue.component('FieldTitle', _vueInstance.$options.components.FieldTitle);
Vue.component('FileUpload', _vueInstance.$options.components.FileUpload);
Vue.component('ModuleProperties', _vueInstance.$options.components.ModuleProperties);
Vue.component('ModuleSetup', _vueInstance.$options.components.ModuleSetup);
Vue.component('ObjectCategories', _vueInstance.$options.components.ObjectCategories);
Vue.component('ObjectCaptions', _vueInstance.$options.components.ObjectCaptions);
Vue.component('ObjectInfo', _vueInstance.$options.components.ObjectInfo);
Vue.component('RelatedObjectsFilter', _vueInstance.$options.components.RelatedObjectsFilter);
Vue.component('Thumbnail', _vueInstance.$options.components.Thumbnail);
Vue.component('RibbonItem', _vueInstance.$options.components.RibbonItem);
Vue.component('UploadedObject', _vueInstance.$options.components.UploadedObject);
Vue.component('ObjectAnnotations', _vueInstance.$options.components.ObjectAnnotations);
