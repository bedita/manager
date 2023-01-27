import { t } from 'ttag';

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
                <button @click.prevent="editMode = !editMode" class="button button-outlined icon-pencil">${t`Edit`}</button>
            </div>

            <div v-if="editMode">
                <input type="text" v-model="name" @change="onChangeName($event)" />
            </div>
            <div v-if="editMode">
                <input type="text" v-model="label" />
            </div>
            <div v-if="editMode">
                <input type="checkbox" v-model="enabled" />
                <label @click="enabled = !enabled">${t`Enabled`}</label>
            </div>
            <div v-if="editMode">
                <: id :>
            </div>
            <div v-if="editMode">
                <button @click.prevent="cancel" class="button button-outlined icon-backward-circled" v-if="obj?.id">${t`Cancel`}</button>
                <button @click.prevent="save" class="button button-primary icon-check-1">${t`Save`}</button>
                <button @click.prevent="remove" class="button button-outlined icon-trash" v-if="obj?.id">${t`Remove`}</button>
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
        this.resetData();
    },

    methods: {

        cancel() {
            this.editMode = !this.editMode;
            this.resetData();
        },

        onChangeName(event) {
            let name = event?.target?.value || '';
            if (!name) {
                return;
            }
            this.name = this.$helpers.slugify(name);
        },

        resetData() {
            this.id = this.obj?.id || '';
            this.name = this.obj?.attributes?.name || '';
            this.label = this.obj?.attributes?.label || '';
            this.enabled = this.obj?.attributes?.enabled || '';
            this.editMode = this.editmode || false;
        },

        async save(event) {
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
                if (response.error) {
                    this.dialog = BEDITA.showError(response.error);
                }
            } catch (error) {
                this.dialog = BEDITA.showError(error);
            } finally {
                event.target.classList.remove('is-loading-spinner');
                this.editMode = false;
            }
        },

        remove(event) {
            this.dialog = BEDITA.confirm(
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
                    this.dialog = BEDITA.showError(`${prefix}: ${response.error}`);
                }
            } catch (error) {
                this.dialog = BEDITA.showError(`${prefix}: ${error}`);
            }
        },
    },
}
