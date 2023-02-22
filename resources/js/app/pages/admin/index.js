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
        // icons
        IconCopy: () => import(/* webpackChunkName: "icon-copy" */'@carbon/icons-vue/es/copy/16.js'),
        IconViewFilled: () => import(/* webpackChunkName: "icon-view-filled" */'@carbon/icons-vue/es/view--filled/16.js'),
        IconViewOffFilled: () => import(/* webpackChunkName: "icon-view-off-filled" */'@carbon/icons-vue/es/view--off--filled/16.js'),
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
