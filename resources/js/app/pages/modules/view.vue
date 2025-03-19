<script>
import { AjaxLogin } from '../../components/ajax-login/ajax-login.js';
import Vue from 'vue';
import { t } from 'ttag';

export default {
    components: {
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */'app/components/coordinates-view'),
        TagPicker: () => import(/* webpackChunkName: "tag-picker" */'app/components/tag-picker/tag-picker'),
        PropertyView: () => import(/* webpackChunkName: "property-view" */'app/components/property-view/property-view'),
        HorizontalTabView: () => import(/* webpackChunkName: "horizontal-tab-view" */'app/components/horizontal-tab-view'),
        ObjectNav: () => import(/* webpackChunkName: "object-nav" */'app/components/object-nav/object-nav'),
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
        ObjectPropertyAdd: () => import(/* webpackChunkName: "object-property-add" */'app/components/object-property/object-property-add'),
        ObjectTypesList: () => import(/* webpackChunkName: "object-types-list" */'app/components/object-types-list/object-types-list'),
        KeyValueList: () => import(/* webpackChunkName: "key-value-list" */'app/components/json-fields/key-value-list'),
        StringList: () => import(/* webpackChunkName: "string-list" */'app/components/json-fields/string-list'),
        LanguageSelector:() => import(/* webpackChunkName: "language-selector" */'app/components/language-selector/language-selector'),
    },

    props: {
        object: {
            type: Object,
            default: () => {},
        },
        userRoles: {
            type: Array,
            default: () => ([]),
        },
    },

    data() {
        return {
            changeListener: null,
            submitListener: null,
            tabsOpen: true,
            errors: [],
        };
    },

    mounted() {
        window.addEventListener('keydown', this.toggleTabs);
        if (this.$refs.formMain) {
            this.submitListener = this.$refs.formMain.addEventListener('submit', this.submitForm);
            this.changeListener = this.$refs.formMain.addEventListener('change', () => window._vueInstance.$emit('resource-changed'));
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
            // avoid multiple submit
            this.$refs.formMain.removeEventListener('submit', this.submitListener, {});

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
                    this.$helpers.handleApiError(json);
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
            } finally {
                el.classList.remove('is-loading-spinner');
                this.processErrors();
            }
        },

        isTranslatable(content) {
            return content && content.length > 0;
        },

        translate(object, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');

            this.fetchTranslation(object)
                .finally(() => {
                    el.classList.remove('is-loading-spinner');
                    this.processErrors();
                });
        },

        enqueueError(error, field, from, to, engine) {
            if (error) {
                console.error(error);
            }
            const message = t`Error translating from "${from}" to "${to}"`;
            const details = t`Could not translate field "${field}" with translation engine "${engine}"`;
            this.errors = [...this.errors, { message, details }]

            return true;
        },

        processErrors() {
            if (this.errors.length === 0) {
                return;
            }
            let details = '';
            for (let e of this.errors) {
                if (e?.details) {
                    if (details.length === 0) {
                        details = e.details;
                        continue;
                    }
                    details = details + '\n' + e.details;
                }
            }
            const title = t`OOOPS! Something went wrong` + '. ' + this.errors[0].message;
            this.$helpers.handleApiError({
                error: {
                    title,
                    details
                }
            });
            this.errors = [];
        },

        fetchTranslation(object) {
            if (!object || !this.isTranslatable(object?.content) || !document.getElementById('translatorEngine')) {
                // skip translation if no content or no translator engine

                return Promise.resolve();
            }
            if (!object.to) {
                // use `value` from select on new translations
                object.to = document.getElementById('translateTo').value;
            }
            object.fromLabel = document.getElementById('translateFromLabel').value;
            object.toLabel = document.getElementById('translateToLabel').value;

            const translator = document.getElementById('translatorEngine').value;

            return this.$helpers.autoTranslate(object.content, object.from, object.to, translator)
                .catch(error => {
                    return this.enqueueError(error, object.field, object.fromLabel, object.toLabel, translator);
                })
                .then(r => {
                    if (!r?.translation) {
                        return this.enqueueError(null, object.field, object.fromLabel, object.toLabel, translator);
                    }
                    if (Array.isArray(r.translation)) {
                        // this to avoid "," could be problematic as separator for contents
                        r.translation = r.translation.join('|||');
                        let allEmpty = true;
                        for (let tr of r.translation) {
                            allEmpty = allEmpty && tr.length === 0;
                        }
                        if (allEmpty) {
                            return this.enqueueError(null, object.field, object.fromLabel, object.toLabel, translator);
                        }
                    }
                    let input = this.$refs[object.field];
                    if (!input) {
                        input = document.getElementById('translated-fields-' + object.field.replaceAll('_', '-'));
                    }
                    input.value = r.translation;
                    input.dispatchEvent(new CustomEvent('change'));
                });
        },
    }
}
</script>
