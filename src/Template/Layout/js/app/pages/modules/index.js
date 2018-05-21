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

export default {
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
            isAllChecked: false,
            all: [],
            checked: [],
            exportids: [],
            statusids: [],
            trashids: [],
            restoreids: [], // used in trash
            status: '',
        };
    },

    /**
     * @inheritDoc
     */
    created() {
        try {
            this.all = JSON.parse(this.ids);
            this.all = this.all.map(Number);
        } catch(error) {
            console.error(error);
        }
    },

    /**
     * watched vars handlers
     */
    watch: {
        /**
         * Checked checkboxes change handler.
         * If necessary, set isAllChecked.
         *
         * @return {void}
         */
        checked() {
            if (!this.isAllChecked && (this.all.length === this.checked.length)) {
                this.isAllChecked = true;
            } else if (this.isAllChecked && (this.all.length > this.checked.length)) {
                this.isAllChecked = false;
            }
            this.exportids = this.checked;
            this.statusids = this.checked;
            this.trashids = this.checked;
            this.restoreids = this.checked;
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
        checkAll() {
            this.isAllChecked = !this.isAllChecked;
            this.checked = (this.isAllChecked) ? this.all : [];
            this.exportids = this.checked;
        },

        /**
         * Submit bulk export form, if at least one item is checked
         *
         * @return {void}
         */
        exportSelected() {
            if (this.checked.length < 1) {
                return;
            }
            document.getElementById('form-export').submit();
        },

        /**
         * Submit bulk change status form, if at least one item is checked and status is not empty
         *
         * @return {void}
         */
        changeStatus() {
            if (this.statusids.length < 1 || !this.status) {
                return;
            }
            document.getElementById('form-status').submit();
        },

        /**
         * Submit bulk delete form, if at least one item is checked
         *
         * @return {void}
         */
        trash() {
            if (this.trashids.length < 1) {
                return;
            }
            document.getElementById('form-delete').submit();
        },

        /**
         * Submit bulk restore form, if at least one item is checked
         *
         * @return {void}
         */
        restore() {
            if (this.restoreids.length < 1) {
                return;
            }
            document.getElementById('form-restore').submit();
        },

        selectRow(event) {
            if(event.target.type != 'checkbox') {
                event.preventDefault();
                event.target.querySelector('input[type=checkbox]').checked = !event.target.querySelector('input[type=checkbox]').checked;
            }
        }
    }
}


