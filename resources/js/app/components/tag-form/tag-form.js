import { t } from 'ttag';
import { confirm, error as showError, info as showInfo } from 'app/components/dialog/dialog';

export default {
    template: `
        <form :class="formClass">
            <div v-if="!editMode">
                <: name :>
            </div>
            <div v-if="!editMode">
                <: label :>
            </div>
            <div v-if="!editMode">
                <input type="checkbox" v-model="enabled" disabled="disabled" />
                <label>${t`Enabled`}</label>
            </div>
            <div v-if="!editMode">
                <: id :>
            </div>
            <div v-if="canSave && !editMode">
                <button @click.prevent="editMode = !editMode" class="button button-outlined">
                    <app-icon icon="carbon:edit"></app-icon>
                    <span class="ml-05">${t`Edit`}</span>
                </button>
            </div>

            <div v-if="editMode">
                <input type="text" size="50" maxlength="50" v-model="name" @change="onChangeName($event)" />
                <span class="ml-05" v-title="this.$helpers.minLength(3)"><app-icon icon="carbon:information"></app-icon></span>
            </div>
            <div v-if="editMode">
                <input type="text" v-model="label" />
                <span class="ml-05" v-title="this.$helpers.minLength(3)"><app-icon icon="carbon:information"></app-icon></span>
            </div>
            <div v-if="editMode">
                <input type="checkbox" v-model="enabled" />
                <label @click="enabled = !enabled">${t`Enabled`}</label>
            </div>
            <div v-if="editMode && id">
                <: id :>
            </div>
            <div v-if="editMode">
                <button @click.prevent="cancel" class="button button-outlined" v-if="obj?.id">
                    <app-icon icon="carbon:undo"></app-icon>
                    <span class="ml-05">${t`Cancel`}</span>
                </button>
                <button @click.prevent="save" class="button button-primary" :disabled="name.length < 3 || label.length < 3">
                    <app-icon icon="carbon:save"></app-icon>
                    <span class="ml-05">${t`Save`}</span>
                </button>
                <button @click.prevent="remove" class="button button-outlined" v-if="obj?.id">
                    <app-icon icon="carbon:trash-can"></app-icon>
                    <span class="ml-05">${t`Remove`}</span>
                </button>
            </div>
        </form>
    `,

    props: {
        cansave: Boolean,
        editmode: Boolean,
        obj: {},
        redir: String,
    },

    data() {
        return {
            dialog: null,
            editMode: false,
            canSave: false,
            id: String,
            name: String,
            label: String,
            enabled: Boolean,
            formClass: 'table-row disabled',
            originalObj: Object,
        };
    },

    watch: {
        enabled(newVal) {
            this.formClass = newVal === true ? 'table-row' : 'table-row disabled';
        }
    },

    async created() {
        this.canSave = this.cansave;
    },

    async mounted() {
        this.originalObj = this.obj || {};
        this.resetData();
    },

    methods: {

        cancel() {
            this.editMode = !this.editMode;
            this.resetData();
        },

        onChangeName(event) {
            const name = event?.target?.value || '';
            if (!name) {
                return;
            }
            this.name = this.$helpers.slugify(name, 50);
        },

        resetData() {
            this.id = this.originalObj?.id || '';
            this.name = this.originalObj?.attributes?.name || '';
            this.label = this.originalObj?.attributes?.label || '';
            this.enabled = this.originalObj?.attributes?.enabled || '';
            this.editMode = this.editmode || false;
        },

        async nameInUse() {
            const headers = new Headers({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': BEDITA.csrfToken,
            });
            const options = {
                credentials: 'same-origin',
                headers,
                method: 'GET',
            };
            const response = await fetch(`${BEDITA.base}/api/model/tags?filter[name]=${this.name}`, options);
            const responseJson = await response.json();

            return responseJson?.data?.length > 0;
        },

        async save(event) {
            if (!this.obj?.id || (this.obj?.id && this.name != this.originalObj?.attributes?.name)) {
                const inUse = await this.nameInUse();
                if (inUse) {
                    const tagName = this.name;
                    this.dialog = showInfo(t`Tag ${tagName} already exists`);
                    this.resetData();

                    return;
                }
            }
            try {
                event.target.classList.add('is-loading-spinner');
                const headers = new Headers({
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': BEDITA.csrfToken,
                });
                const payload = {
                    data: {
                        type: 'tags',
                        attributes: {
                            name: this.name,
                            label: this.label,
                            enabled: this.enabled || false
                        }
                    }
                };
                if (this.obj?.id) {
                    payload.data.id = this.obj.id;
                }
                const options = {
                    credentials: 'same-origin',
                    headers,
                };
                let response = {};
                if (this.obj?.id) {
                    payload.data.id = this.obj.id;
                    options.body = JSON.stringify(payload);
                    options.method = 'PATCH'
                    response = await fetch(`${BEDITA.base}/api/model/tags/${this.obj.id}`, options);
                } else {
                    options.body = JSON.stringify(payload);
                    options.method = 'POST'
                    response = await fetch(`${BEDITA.base}/api/model/tags`, options);
                }
                if (response.status === 200) {
                    if (this.redir) {
                        window.location = this.redir;
                    }
                    return;
                }

                if (response?.error) {
                    this.dialog = showError(response.error);
                } else if (response.status >= 400) {
                    this.dialog = showError(t`Error while saving tag`);
                }
            } catch (error) {
                this.dialog = showError(error);
            } finally {
                event.target.classList.remove('is-loading-spinner');
                this.editMode = false;
            }
        },

        remove(event) {
            this.dialog = confirm(
                t`If you confirm, this resource will be gone forever. Are you sure?`,
                t`yes, proceed`,
                () => {
                    event.target.classList.add('is-loading-spinner');
                    this.delete();
                }
            );
        },

        async delete() {
            const prefix = t`Error on deleting tag`;
            try {
                const options = {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                };
                const response = await fetch(`${BEDITA.base}/api/model/tags/${this.obj.id}`, options);
                if (response.status === 200) {
                    this.$el.remove(this.$el);
                    this.dialog?.hide();

                    return;
                }
                if (response.error) {
                    this.dialog = showError(`${prefix}: ${response.error}`);
                }
            } catch (error) {
                this.dialog = showError(`${prefix}: ${error}`);
            }
        },
    },
}
