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
        DateRangesList: () => import(/* webpackChunkName: "date-ranges-list" */'app/components/date-ranges-list/date-ranges-list'),
        TreeView: () => import(/* webpackChunkName: "tree-view" */'app/components/tree-view/tree-view'),
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
            console.log(val)
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
        },
        unCheckAll() {
            this.selectedRows = [];
        },

        /**
         * Submit bulk actions form
         *
         * @return {void}
         */
        bulkActions() {
            if (this.selectedRows.length < 1) {
                return;
            }
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
}
