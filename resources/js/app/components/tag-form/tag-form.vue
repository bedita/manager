<template>
    <form class="tag-form" :class="formClass">
        <div v-if="!editMode">
            {{ name }}
        </div>
        <div v-if="!editMode">
            {{ label }}
        </div>
        <div v-if="!editMode">
            <input type="checkbox" v-model="enabled" disabled="disabled" />
            <label>{{ msgEnabled }}</label>
        </div>
        <div v-if="canSave && !editMode">
            <button @click.prevent="editMode = !editMode" class="button button-outlined">
                <app-icon icon="carbon:edit"></app-icon>
                <span class="ml-05">{{ msgEdit }}</span>
            </button>
        </div>

        <div v-if="editMode">
            <input type="text" size="50" maxlength="50" v-model="name" @change="onChangeName($event)" />
            <span class="ml-05" v-title="this.$helpers.minLength(3)"><app-icon icon="carbon:information"></app-icon></span>
        </div>
        <div v-if="editMode">
            <labels-form
                :labels-source="labels || {'default':''}"
                :language="language"
                :languages="languagesOptions"
                @change="changeLabels"
            >
            </labels-form>
        </div>
        <div v-if="editMode">
            <input type="checkbox" v-model="enabled" />
            <label @click="enabled = !enabled">{{ msgEnabled }}</label>
        </div>
        <div v-if="editMode">
            <button @click.prevent="cancel" class="button button-outlined" v-if="obj?.id">
                <app-icon icon="carbon:undo"></app-icon>
                <span class="ml-05">{{ msgCancel }}</span>
            </button>
            <button @click.prevent="save" class="button button-primary" :disabled="name.length < 3 || labels?.['default']?.length < 3">
                <app-icon icon="carbon:save"></app-icon>
                <span class="ml-05">{{ msgSave }}</span>
            </button>
            <button @click.prevent="remove" class="button button-outlined" v-if="obj?.id">
                <app-icon icon="carbon:trash-can"></app-icon>
                <span class="ml-05">{{ msgRemove }}</span>
            </button>
        </div>
    </form>
</template>
<script>
import { t } from 'ttag';
import { confirm, error as showError, info as showInfo } from 'app/components/dialog/dialog';

export default {
    name: 'TagForm',

    components: {
        LabelsForm:() => import(/* webpackChunkName: "labels-form" */'app/components/labels-form'),
    },

    props: {
        cansave: Boolean,
        editmode: Boolean,
        language: {
            type: String,
            default: '',
        },
        languages: {
            type: Object,
            default: () => {},
        },
        obj: {},
        redir: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            dialog: null,
            editMode: false,
            canSave: false,
            id: String,
            name: String,
            label: String,
            labels: Object,
            enabled: Boolean,
            formClass: 'table-row disabled',
            languagesOptions: Array,
            originalObj: Object,
            msgCancel: t`Cancel`,
            msgEdit: t`Edit`,
            msgEnabled: t`Enabled`,
            msgRemove: t`Remove`,
            msgSave: t`Save`,
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
        this.$nextTick(() => {
            this.originalObj = this.obj || {};
            this.resetData();
            this.languages['null'] = '';
            this.languagesOptions = Object.keys(this.languages).map((key) => {
                return {
                    id: key,
                    label: this.languages[key],
                };
            });
        });
    },

    methods: {

        cancel() {
            this.editMode = !this.editMode;
            this.resetData();
        },

        changeLabels(labels) {
            this.labels = labels;
            this.saveDisabled = this.unchanged() || this.name.length < 3 || labels?.['default']?.length < 3;
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
            this.labels = { ...this.originalObj?.attributes?.labels || {} };
            this.enabled = this.originalObj?.attributes?.enabled || '';
            this.editMode = this.editmode || false;
            if (this.name === 'ligotti') {
                console.log(this.originalObj);
            }
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
                            labe: this.label,
                            labels: this.labels,
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

        unchanged() {
            return this.name === this.originalObj.name && JSON.stringify(this.labels) === JSON.stringify(this.originalObj.labels) && this.enabled === this.originalObj.enabled;
        },
    },
}
</script>
<style>
.tag-form {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    text-align: left;
    align-items: center;
    gap: 8px;
    margin: 4px 0;
}
.tag-form > div {
    display: flex;
    align-items: center;
    margin: 4px 0;
}
.tag-form > div > button {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    margin: 4px;
    cursor: pointer;
    width: 100%;
}
</style>
