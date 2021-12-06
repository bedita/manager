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
        PropertyView: () => import(/* webpackChunkName: "property-view" */'app/components/property-view/property-view'),
        HorizontalTabView: () => import(/* webpackChunkName: "horizontal-tab-view" */'app/components/horizontal-tab-view'),
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
        this.$refs.formMain.addEventListener('submit', this.submitForm);
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

                const json = await response.json();
                if (json.error) {
                    await this.showFlashMessages();
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
