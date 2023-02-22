import { AjaxLogin } from '../../components/ajax-login/ajax-login.js';
import Vue from 'vue';

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

export default {
    components: {
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */'app/components/coordinates-view'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        PropertyView: () => import(/* webpackChunkName: "property-view" */'app/components/property-view/property-view'),
        HorizontalTabView: () => import(/* webpackChunkName: "horizontal-tab-view" */'app/components/horizontal-tab-view'),
        ObjectNav: () => import(/* webpackChunkName: "object-nav" */'app/components/object-nav/object-nav'),
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
        ObjectTypesList: () => import(/* webpackChunkName: "object-types-list" */'app/components/object-types-list/object-types-list'),
        // icons
        IconChevronLeft: () => import(/* webpackChunkName: "icon-chevron-left" */'@carbon/icons-vue/es/chevron--left/32.js'),
        IconChevronRight: () => import(/* webpackChunkName: "icon-chevron-right" */'@carbon/icons-vue/es/chevron--right/32.js'),
        IconClose: () => import(/* webpackChunkName: "icon-close" */'@carbon/icons-vue/es/close/16.js'),
        IconDownload: () => import(/* webpackChunkName: "icon-download" */'@carbon/icons-vue/es/download/16.js'),
        IconExport: () => import(/* webpackChunkName: "icon-export" */'@carbon/icons-vue/es/export/16.js'),
        IconLaunch: () => import(/* webpackChunkName: "icon-launch" */'@carbon/icons-vue/es/launch/16.js'),
        IconLocked: () => import(/* webpackChunkName: "icon-locked" */'@carbon/icons-vue/es/locked/16.js'),
        IconReplicate: () => import(/* webpackChunkName: "icon-replicate" */'@carbon/icons-vue/es/replicate/16.js'),
        IconSave: () => import(/* webpackChunkName: "icon-save" */'@carbon/icons-vue/es/save/16.js'),
        IconStop: () => import(/* webpackChunkName: "icon-stop" */'@carbon/icons-vue/es/stop/16.js'),
        IconTrashCan16: () => import(/* webpackChunkName: "icon-trash-can-16" */'@carbon/icons-vue/es/trash-can/16.js'),
        IconUndo: () => import(/* webpackChunkName: "icon-undo" */'@carbon/icons-vue/es/undo/16.js'),
        IconUnlink: () => import(/* webpackChunkName: "icon-unlink" */'@carbon/icons-vue/es/unlink/16.js'),
        IconUnlocked: () => import(/* webpackChunkName: "icon-unlocked" */'@carbon/icons-vue/es/unlocked/16.js'),
        IconView: () => import(/* webpackChunkName: "icon-view" */'@carbon/icons-vue/es/view/16.js'),
        IconWikis16: () => import(/* webpackChunkName: "icon-wikis-16" */'@carbon/icons-vue/es/wikis/16.js'),
    },

    props: {
        object: Object,
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            tabsOpen: true,
        };
    },

    mounted() {
        window.addEventListener('keydown', this.toggleTabs);
        if (this.$refs.formMain) {
            this.$refs.formMain.addEventListener('submit', this.submitForm);
        }
    },

    methods: {
        toggleTabs(e) {
            let key = e.which || e.keyCode || 0;
            if (key === 27) {
                return this.tabsOpen = !this.tabsOpen;
            }
        },

        submitForm(event) {
            event.preventDefault();
            event.stopPropagation();

            const form = event.target;
            if (form.disabled) {
                return;
            }

            const button = document.querySelector('button[form=form-main]');
            button.classList.add('is-loading-spinner');
            const formData = new FormData(event.target);
            const action = event.target.getAttribute('action');

            form.disabled = true;

            const ajaxCall = fetch(action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData,
                mode: 'same-origin',
                credentials: 'same-origin',
                redirect: 'manual',
            }).then(async (response) => {
                button.classList.remove('is-loading-spinner');
                if (!response.ok) {
                    // a redirect was performed; we assume it was to /login page
                    if (response.status === 0 && response.type === 'opaqueredirect') {
                        console.warn('session expired');
                        this.renewSession();
                        throw new Error('Unauthorized');
                    }

                    const error = await response.text();
                    console.error(error);
                    throw new Error(error);
                }

                let json;
                try {
                    json = await response.json();
                } catch (e) {
                    console.error('Malformed json response on save');
                    window.location.reload();
                }
                if (json?.error) {
                    await this.showFlashMessages();
                    BEDITA.error(json.error);
                    throw new Error(json.error);
                }

                // clear form dirty state, to avoid alert message about unsaved changes before changing page
                window._vueInstance.dataChanged.clear();
                window.location = this.$helpers.buildViewUrlType(json.data[0].type, json.data[0].id);
            });

            ajaxCall
                .catch(() => {
                    form.disabled = false;
                });

            return ajaxCall;
        },

        renewSession() {
            const iframe = new AjaxLogin({
                propsData: {
                    headerText: 'Login',
                    message: 'Session expired, login to continue editing the object',
                }
            });
            iframe.$mount();
            document.body.appendChild(iframe.$el);
        },

        async showFlashMessages() {
            // fetch flash messages template
            const messages = await fetch('/dashboard/messages');
            const html = await messages.text();
            // create new element and append to DOM
            const element = document.createElement('div');
            element.innerHTML = html.trim();
            this.$root.$el.appendChild(element);
            // create new Vue instance to handle flash messages template
            const flashInstance = new Vue({
                el: element.firstElementChild,
                components: {
                    FlashMessage: () => import(/* webpackChunkName: "flash-message" */'app/components/flash-message'),
                },
            });
            // cleanup on flash message close
            window._vueInstance.$once('flash-message:closed', () => {
                flashInstance.$destroy();
                element.remove();
            });
        },

        async translateAll(data, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');
            try {
                await Promise.all(
                    Object.keys(data).map(key =>
                        this.fetchTranslation(data[key])
                    )
                );
            } catch (error) {
                alert(error);
                console.log(error);
            }
            el.classList.remove('is-loading-spinner');
        },

        translate(object, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');

            this.fetchTranslation(object)
                .catch((error) => {
                    alert(error);
                    console.log(error);
                })
                .finally(() => {
                    el.classList.remove('is-loading-spinner');
                });
        },

        fetchTranslation(object) {
            if (!object.content) {
                return;
            }
            if (!object.to) {
                // use `value` from select on new translations
                object.to = this.$refs.translateTo.value;
            }

            return this.$helpers.autoTranslate(object.content, object.from, object.to)
                .catch(error => {
                    console.error(error);

                    throw new Error(`Unable to translate field ${object.field}`);
                })
                .then(r => {
                    if (!r.translation) {
                        throw new Error(`Unable to translate field ${object.field}`);
                    }

                    let input = this.$refs[object.field];
                    input.value = r.translation;
                    input.dispatchEvent(new CustomEvent('change'));
                });
        },
    }
}
