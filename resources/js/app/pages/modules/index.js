import { confirm } from 'app/components/dialog/dialog';
import { t } from 'ttag';

/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Modules/index.twig
 *
 * <modules-index> component used for ModulesPage -> Index
 *
 */
export default {
    components: {
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */'app/components/category-picker/category-picker'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        FilterBoxView: () => import(/* webpackChunkName: "tree-view" */'app/components/filter-box'),
        IndexCell: () => import(/* webpackChunkName: "index-cell" */'app/components/index-cell/index-cell'),
        // icons
        IconAdd: () => import(/* webpackChunkName: "icon-add" */'@carbon/icons-vue/es/add/32.js'),
        IconCategories: () => import(/* webpackChunkName: "icon-categories" */'@carbon/icons-vue/es/categories/16.js'),
        IconCheckmark: () => import(/* webpackChunkName: "icon-checkmark" */'@carbon/icons-vue/es/checkmark/16.js'),
        IconCopy: () => import(/* webpackChunkName: "icon-copy" */'@carbon/icons-vue/es/copy/16.js'),
        IconDocument: () => import(/* webpackChunkName: "icon-document" */'@carbon/icons-vue/es/document/16.js'),
        IconExport: () => import(/* webpackChunkName: "icon-export" */'@carbon/icons-vue/es/export/16.js'),
        IconFilter: () => import(/* webpackChunkName: "icon-filter" */'@carbon/icons-vue/es/filter/16.js'),
        IconFilterEdit: () => import(/* webpackChunkName: "icon-filter-edit" */'@carbon/icons-vue/es/filter--edit/16.js'),
        IconFilterReset: () => import(/* webpackChunkName: "icon-filter-reset" */'@carbon/icons-vue/es/filter--reset/16.js'),
        IconHelp: () => import(/* webpackChunkName: "icon-help" */'@carbon/icons-vue/es/help/16.js'),
        IconHourglass: () => import(/* webpackChunkName: "icon-hourglass" */'@carbon/icons-vue/es/hourglass/16.js'),
        IconLaunch: () => import(/* webpackChunkName: "icon-launch" */'@carbon/icons-vue/es/launch/16.js'),
        IconRedo: () => import(/* webpackChunkName: "icon-redo" */'@carbon/icons-vue/es/redo/16.js'),
        IconSearch: () => import(/* webpackChunkName: "icon-search" */'@carbon/icons-vue/es/search/16.js'),
        IconTrashCan16: () => import(/* webpackChunkName: "icon-trash-can-16" */'@carbon/icons-vue/es/trash-can/16.js'),
    },

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
            bulkField: null,
            bulkValue: null,
            bulkAction: null,
            selectedIds: null,
            /**
             * Selected folder for bulk copy or move.
             * Used to enable/disable confirmation button.
             */
            bulkFolder: null,
        };
    },

    /**
     * @inheritDoc
     */
    created() {
        try {
            this.allIds = JSON.parse(this.ids);
        } catch (error) {
            console.error(error);
        }
    },

    computed: {
        allChecked() {
            return JSON.stringify(this.selectedRows.sort()) == JSON.stringify(this.allIds.sort());
        }
    },

    watch: {
        selectedRows(val) {
            if (!val.length) {
                this.$refs.checkAllCB.checked = false;
                this.$refs.checkAllCB.indeterminate = false;
            } else if (val.length == this.allIds.length) {
                this.$refs.checkAllCB.checked = true;
                this.$refs.checkAllCB.indeterminate = false;
            } else {
                this.$refs.checkAllCB.indeterminate = true;
            }
        },
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
            for (let id of this.allIds) {
                let row = document.querySelector(`a[data-id="${id}"]`);
                this.rowBackground(row, true);
            }
        },
        unCheckAll() {
            this.selectedRows = [];
            for (let id of this.allIds) {
                let row = document.querySelector(`a[data-id="${id}"]`);
                this.rowBackground(row, false);
            }
        },

        /**
         * Submit bulk actions form
         *
         * @param {String} formId The form ID
         * @return {void}
         */
        bulkActions(formId) {
            if (this.selectedRows.length < 1) {
                return;
            }
            document.querySelector(`form#${formId}`).submit();
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
         * Submit bulk export form for all objects by type
         *
         * @return {void}
         */
        exportAll() {
            this.unCheckAll();
            this.$nextTick(() => {
                document.getElementById('form-export').submit();
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

            let number = this.selectedRows.length;
            let message = t`Moving ${number} item(s) to trash. Are you sure?`;
            let form = document.getElementById('form-delete');
            confirm(message, t`yes, proceed`, form.submit.bind(form));
        },

        /**
         * selects a row when triggered from a container target that is parent of the relative checkbox
         *
         * @return {void}
         */
        selectRow(event) {
            if (event.target.type != 'checkbox') {
                event.preventDefault();
                let cb = event.target.querySelector('input[type=checkbox]');
                let position = this.selectedRows.indexOf(cb.value);
                if (position != -1) {
                    this.selectedRows.splice(position, 1);
                } else {
                    this.selectedRows.push(cb.value);
                }
            } else {
                let row = event.target.closest('a.table-row');
                let checked = event.target.checked;
                this.rowBackground(row, checked);
            }
        },

        rowBackground(row, checked) {
            if (checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        },

        onUpdatePageSize(event) {
            window._vueInstance.$emit('filter-update-page-size', event);
        },

        onUpdateCurrentPage(event) {
            window._vueInstance.$emit('filter-update-current-page', event);
        },

        goto(url) {
            window.location.href = url;
        },
    }
}
