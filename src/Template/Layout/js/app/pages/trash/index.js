/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Trash/index.twig
 *
 * <trash-index> component used for TrashPage -> Index
 *
 * @extends ModulesIndex
 */

import ModulesIndex from 'app/pages/modules/index';
import { confirm } from 'app/components/dialog/dialog';
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
            let number = this.selectedRows.length;
            if (number < 1) {
                return;
            }

            let form = document.getElementById('form-delete');
            confirm(
                t`Do you confirm the deletion of ${number} item from the trash?`,
                t`yes, proceed`,
                form.submit.bind(form)
            );
        },
    }
}
