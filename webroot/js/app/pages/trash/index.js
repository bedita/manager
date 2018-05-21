const ModulesIndex = Vue.options.components["modules-index"];

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
Vue.component('trash-index', {
    extends: ModulesIndex,
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


