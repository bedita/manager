/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Pages/Admin/../*.twig
 *
 * <admin-index> component used for Admin index pages
 */

import { confirm } from 'app/components/dialog/dialog';
import { t } from 'ttag';

export default {
    methods: {
        remove(e) {
            const message = t`Remove item. Are you sure?`;
            const form = document.getElementById(e.target.getAttribute('form'));
            confirm(message, t`yes, proceed`, form.submit.bind(form));
        },
    }
};
