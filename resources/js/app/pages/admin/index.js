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
        ShowHide:() => import(/* webpackChunkName: "show-hide" */'app/components/show-hide/show-hide'),
    },
    data() {
        return {
            tabsOpen: true,
            showCreate: false,
        };
    },
    methods: {
        toggleCreate() {
            this.showCreate = !this.showCreate;
            if (!this.showCreate) {
                return;
            }
            this.$nextTick(() => {
                const form = document.getElementById('form-create');
                form?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                form?.querySelector('input:not([type=hidden]), select, textarea')?.focus({ preventScroll: true });
            });
        },
        remove(e) {
            const message = t`Remove item. Are you sure?`;
            const formId = e.target.closest('button').getAttribute('form');
            const form = document.getElementById(formId);
            confirm(message, t`yes, proceed`, form.submit.bind(form));
        },
    }
};
