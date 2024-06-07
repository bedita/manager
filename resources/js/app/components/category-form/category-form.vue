<template>
    <form class="table-row">
        <div class="name-cell">
            <input type="text" name="name" autocomplete="off" autocorrect="off" autocapitalize="off" size="30" maxlength="50" @change="onChangeName($event)" v-model="name" />
            <span class="ml-05" v-title="this.$helpers.minLength(3)"><app-icon icon="carbon:information"></app-icon></span>
            <div v-if="nameInUse()" v-text="msgErrorAlreadyInUse"></div>
        </div>
        <div class="label-cell">
            <labels-form
                :labels-source="labels"
                :language="language"
                :languages="languagesOptions"
                @change="changeLabels"
            >
            </labels-form>
        </div>
        <template v-if="options?.showType">
            <div class="object_type_name-cell" v-show="!selectTypes">
                <input type="hidden" name="object_type_name" v-model="type" />
                <span :class="typeClass()">{{ type }}</span>
            </div>
            <div class="object_type_name-cell" v-show="selectTypes">
                <select v-model="type" @change="updateType(type, $event)">
                    <option v-for="t in original.types" :value="t">{{ t }}</option>
                </select>
            </div>
        </template>
        <div class="parent_id-cell">
            <select v-model="parent" @change="onChangeParent" class="parent" :disabled="!type">
                <template v-for="parent,pId in parentsByType()">
                    <option :value="parent.id" v-if="parent.id != source.id">{{ parent.label }}</option>
                </template>
            </select>
        </div>
        <div class="enabled-cell">
            <input type="checkbox" name="enabled" v-model="enabled" checked="!!enabled" />
        </div>
        <div v-show="!id" class="buttons-cell narrow">
            <button :disabled="name.length < 3 || labels?.['default']?.length < 3 || nameInUse() || type === ''" class="button button-text-white is-width-auto" @click.stop.prevent="onCreate()">
                <app-icon icon="carbon:save"></app-icon>
                <span class="ml-05">{{ msgCreate }}</span>
            </button>
        </div>
        <template v-if="options?.showId">
            <div v-show="id">
                {{ id }}
            </div>
        </template>
        <div v-show="id" class="buttons-cell narrow">
            <button
                :disabled="loading || saveDisabled"
                class="button button-text-white is-width-auto"
                :class="{'is-loading-spinner': loading}"
                @click.stop.prevent="onModify()"
            >
                <app-icon icon="carbon:save"></app-icon>
                <span class="ml-05">{{ msgModify }}</span>
            </button>
        </div>
        <div v-show="id" class="buttons-cell narrow">
            <button class="button button-text-white is-width-auto" @click.stop.prevent="onDelete()">
                <app-icon icon="carbon:trash-can"></app-icon>
                <span class="ml-05">{{ msgDelete }}</span>
            </button>
        </div>
    </form>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'CategoryForm',

    components: {
        LabelsForm:() => import(/* webpackChunkName: "labels-form" */'app/components/labels-form'),
    },

    props: {
        language: {
            type: String,
            default: '',
        },
        languages: {
            type: Object,
            default: () => {},
        },
        source: {
            type: Object,
            default: () => {},
        },
        parents: {
            type: Object,
            default: () => {},
        },
        names: {
            type: Array,
            default: () => ([]),
        },
        allnames: {
            type: Object,
            default: () => {},
        },
        options: {
            type: Object,
            default: () => {
                return {
                    'showId': false,
                    'showType': false
                };
            },
        },
    },

    data() {
        return {
            original: Object,
            id: String,
            name: String,
            label: String,
            labels: Object,
            type: String,
            parent: String,
            saveDisabled: true,
            enabled: true,
            selectTypes: false,
            types: Array,
            languagesOptions: Array,
            namesInUse: Array,
            loading: false,
            msgCreate: t`Create`,
            msgErrorAlreadyInUse: t`Name already in use`,
            msgModify: t`Save`,
            msgDelete: t`Delete`,
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.original = { ...this.source };
            this.id = this.original?.id || '';
            this.name = this.original?.name || '';
            this.label = this.original?.label || this.original?.labels?.default || '';
            this.labels = { ...this.original?.labels || {'default': this.label} || {'default': ''} };
            this.type = this.original?.type || '';
            if (!this.type && this.original?.types) {
                this.selectTypes = true;
                this.namesInUse = [];
            } else {
                this.namesInUse = this.names || [];
            }
            this.enabled = this.original?.enabled === undefined ? true :  this.original.enabled;
            this.parent = this.original?.parent_id;
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

        onChangeName(event) {
            const name = event?.target?.value || '';
            if (!name) {
                return;
            }
            this.name = this.$helpers.slugify(name, 50);
            this.saveDisabled = this.unchanged() || !this.valid();
        },

        onChangeParent() {
            this.saveDisabled = this.unchanged() || !this.valid();
        },

        parentsByType() {
            if (!this.type) {
                return [];
            }
            let parents = Object.values(this.parents) || [];
            if (!parents) {
                return [];
            }
            parents = parents.filter((parent) => parent?.object_type_name === this.type) || [];

            return parents.sort((a, b) => {
                if (a.label && b.label) {
                    return a.label.localeCompare(b.label);
                }
                return a.name.localeCompare(b.name);
            });
        },

        updateType() {
            this.namesInUse = this.allnames[this.type] || [];
            this.saveDisabled = this.unchanged() || !this.valid();
        },

        unchanged() {
            return this.name === this.source.name && JSON.stringify(this.labels) === JSON.stringify(this.source.labels) && this.enabled === this.source.enabled && this.parent === this.source.parent_id;
        },

        async onCreate() {
            const prefix = t`Error while creating new category`;
            await this.save(prefix);
        },

        changeLabels(labels) {
            this.labels = labels;
            this.label = labels?.['default'] || '';
            this.saveDisabled = this.unchanged() || !this.valid();
        },

        async save(prefix) {
            try {
                this.loading = true;
                const formData = new FormData();
                if (this.id) {
                    formData.set('id', this.id);
                }
                formData.set('name', this.name);
                formData.set('label', this.label || this.labels?.['default'] || '');
                formData.set('labels', JSON.stringify(this.labels || {'default': this.label || ''}));
                formData.set('enabled', this.enabled);
                formData.set('object_type_name', this.type);
                formData.set('parent_id', this.parent);
                const options = {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                    body: formData,
                };
                const response = await fetch(`/${this.type}/categories/save`, options);
                const responseJson = await response.json();
                if (responseJson.error) {
                    this.handleError(responseJson.error, prefix);
                }
                if (response.status === 200) {
                    document.location.reload();
                }
            } catch (error) {
                this.handleError(error, prefix);
            } finally {
                this.saveDisabled = this.unchanged() || !this.valid();
                this.loading = false;
            }
        },

        async onModify() {
            if (this.unchanged()) {
                return;
            }
            const prefix = t`Error while saving category`;
            await this.save(prefix);
        },

        async onDelete() {
            BEDITA.confirm(
                t`Remove item. Are you sure?`,
                t`yes, proceed`,
                () => {
                    this.delete();
                }
            );
        },

        async delete() {
            const prefix = t`Error on deleting category`;
            try {
                const formData = new FormData();
                formData.set('object_type_name', this.type);
                const options = {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                    body: formData,
                };
                const response = await fetch(`/${this.type}/categories/remove/${this.id}`, options);
                const responseJson = await response.json();
                if (responseJson.error) {
                    this.handleError(responseJson.error, prefix);

                    return;
                }
                document.location.reload();
            } catch (error) {
                this.handleError(error, prefix);
            }
        },

        handleError(error, prefix) {
            console.error(error);
            BEDITA.showError(`${prefix}: ${error}`);
        },

        nameInUse() {
            if (this.original?.name === this.name) {
                return false;
            }
            if (this.namesInUse.length === 0) {
                return false;
            }

            return Array.from(this.namesInUse).includes(this.name);
        },

        typeClass() {
            return `tag has-background-module-${this.type}`;
        },

        valid() {
            if (!this.name) {
                return false;
            }

            return !this.nameInUse();
        },
    },
}
</script>
<style>
select.parent {
    min-width: 100px;
}
</style>
