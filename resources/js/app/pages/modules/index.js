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
    name: 'ModulesIndex',
    components: {
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */'app/components/category-picker/category-picker'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
        TreeCompact: () => import(/* webpackChunkName: "tree-compact" */'app/components/tree-compact/tree-compact'),
        FilterBoxView: () => import(/* webpackChunkName: "tree-view" */'app/components/filter-box'),
        IndexCell: () => import(/* webpackChunkName: "index-cell" */'app/components/index-cell/index-cell'),
        PermissionToggle: () => import(/* webpackChunkName: "permission-toggle" */'app/components/permission-toggle/permission-toggle'),
        DropUpload: () => import(/* webpackChunkName: "drop-upload" */'app/components/drop-upload'),
        CalendarView: () => import(/* webpackChunkName: "calendar-view" */'app/components/calendar-view/calendar-view'),
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
        objects: {
            type: String,
            default: () => '[]',
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
            objectsList: JSON.parse(this.objects),
            selectedObjects: '[]',
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
            if (!this.$refs.checkAllCB) {
                return;
            }
            if (!val.length) {
                this.$refs.checkAllCB.checked = false;
                this.$refs.checkAllCB.indeterminate = false;
            } else if (val.length == this.allIds.length) {
                this.$refs.checkAllCB.checked = true;
                this.$refs.checkAllCB.indeterminate = false;
            } else {
                this.$refs.checkAllCB.indeterminate = true;
            }
            const selectedObjects = this.objectsList.filter((o) => val.includes(o.id));
            this.selectedObjects = JSON.stringify(selectedObjects);
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
            const oo = this.objectsList.map((o) => { return {id: o.id, type: o.type}; });
            const selectedObjects = JSON.parse(JSON.stringify(oo));
            this.selectedObjects = JSON.stringify(selectedObjects);
            this.selectedRows = JSON.parse(JSON.stringify(this.allIds));
            for (let id of this.allIds) {
                let row = document.querySelector(`a[data-id="${id}"]`);
                this.rowBackground(row, true);
            }
        },
        unCheckAll() {
            this.selectedObjects = '[]';
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
                // push into selectedObjects, if still not present
                const obj = this.objectsList.find((o) => o.id == cb.value);
                if (obj) {
                    const selectedObjects = JSON.parse(this.selectedObjects);
                    const objPosition = selectedObjects.findIndex((o) => o.id == cb.value);
                    if (objPosition == -1) {
                        selectedObjects.push({id: obj.id, type: obj.type});
                    } else {
                        selectedObjects.splice(objPosition, 1);
                    }
                    this.selectedObjects = JSON.stringify(selectedObjects);
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
                this.uploaded.push(item);
            }
            this.$forceUpdate();
        },

        extension(filename) {
            return filename.split('.').pop();
        },

        formatDate(d) {
            return this.$helpers.formatDate(d);
        },
    }
}
