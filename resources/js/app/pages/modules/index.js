/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Modules/index.twig
 *
 * <modules-index> component used for ModulesPage -> Index
 *
 */
import { confirm } from 'app/components/dialog/dialog';
import { t } from 'ttag';

export default {
    components: {
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */'app/components/category-picker/category-picker'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        FilterBoxView: () => import(/* webpackChunkName: "tree-view" */'app/components/filter-box'),
        IndexCell: () => import(/* webpackChunkName: "index-cell" */'app/components/index-cell/index-cell'),
        PermissionToggle: () => import(/* webpackChunkName: "permission-toggle" */'app/components/permission-toggle/permission-toggle'),
        DropUpload: () => import(/* webpackChunkName: "drop-upload" */'app/components/drop-upload'),
    },

    /**
     * Component properties
     *
     * @type {Object} props properties
     */
    props: {
        ids: {
            type: String,
            default: () => ([]),
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
            uploaded: [],
        };
    },

    /**
     * @inheritDoc
     */
    async created() {
        try {
            this.allIds = JSON.parse(this.ids);
        } catch (error) {
            console.error(error);
        }
    },

    computed: {
        allChecked() {
            return JSON.stringify(this.selectedRows.sort()) == JSON.stringify(this.allIds.sort());
        },
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
         * @param {Event} event The event
         * @param {String} formId The form ID
         * @return {void}
         */
        bulkActions(event, formId) {
            if (this.selectedRows.length < 1) {
                return;
            }
            event.target.classList.add('is-loading-spinner');
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

        appendUploaded(data) {
            for (let item of data) {
                console.log(item);
                this.uploaded.push(item);
            }
            this.$forceUpdate();
        },

        extension(filename) {
            return filename.split('.').pop();
        },
    }
}
