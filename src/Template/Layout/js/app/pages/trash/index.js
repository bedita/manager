/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Trash/index.twig
 *
 * <trash-index> component used for TrashPage -> Index
 *
 * @extends ModulesIndex
 */

import ModulesIndex from 'app/pages/modules/index';
import { t } from 'ttag';

export default {
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

            let number = this.selectedRows.length;
            let message = t`Do you confirm the deletion of ${number} item from the trash?`;
            let form = document.getElementById('form-delete');
            let dialog = this.$root.$refs.beditaDialog;
            dialog.confirm(message, t`yes, proceed`, form.submit.bind(form));
        },
    }
}
