/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Pages/Admin/../*.twig
 *
 * <admin-index> component used for Admin index pages
 */

import { confirm } from 'app/components/dialog/dialog';
import { t } from 'ttag';

export default {
    components: {
        PropertyView: () => import(/* webpackChunkName: "property-view" */'app/components/property-view/property-view'),
        Secret: () => import(/* webpackChunkName: "secret" */'app/components/secret/secret'),
    },
    data() {
        return {
            tabsOpen: true,
        };
    },
    methods: {
        remove(e) {
            const message = t`Remove item. Are you sure?`;
            const form = document.getElementById(e.target.getAttribute('form'));
            confirm(message, t`yes, proceed`, form.submit.bind(form));
        },
    }
};
