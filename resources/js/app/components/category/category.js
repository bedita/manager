import { t } from 'ttag';
import { error as showError } from 'app/components/dialog/dialog';

export default {
    template: `
        <form class="table-row">
            <div class="name-cell">
                <input type="text" name="name" autocomplete="off" autocorrect="off" autocapitalize="off" v-model="name" />
                <div v-if="nameInUse()" v-text="errorAlreadyInUse"></div>
            </div>
            <div class="label-cell">
                <input type="text" name="label" autocomplete="off" autocorrect="off" autocapitalize="off" v-model="label" />
            </div>
            <div class="parent_id-cell">
                <select v-model="parent">
                    <option v-for="pLabel,pId in parents" :value="pId"><: pLabel :></option>
                </select>
            </div>
            <div class="object_type_name-cell" v-show="!selectTypes">
                <input type="hidden" name="object_type_name" v-model="type" />
                <span :class="typeClass()"><: type :></span>
            </div>
            <div class="object_type_name-cell" v-show="selectTypes">
                <select v-model="type" @change="updateType(type, $event)">
                    <option v-for="t in source.types" :value="t"><: t :></option>
                </select>
            </div>
            <div class="enabled-cell">
                <input type="checkbox" name="enabled" v-model="enabled" checked="!!enabled" />
            </div>
            <div v-show="!id" class="buttons-cell narrow">
                <button :disabled="name === '' || nameInUse() || type === ''" class="button button-text-white is-width-auto" @click.stop.prevent="onCreate()">${t`Create`}</button>
            </div>
            <div v-show="id">
                <: id :>
            </div>
            <div v-show="id" class="buttons-cell narrow">
                <button :disabled="name === '' || unchanged() || nameInUse()" class="button button-text-white is-width-auto" @click.stop.prevent="onModify()">${t`Modify`}</button>
            </div>
            <div v-show="id" class="buttons-cell narrow">
                <button class="button button-text-white is-width-auto" @click.stop.prevent="onDelete()">${t`Delete`}</button>
            </div>
        </form>
    `,

    props: {
        source: Object,
        parents: Object,
        names: Array,
        allnames: Object,
    },

    data() {
        return {
            original: Object,
            id: String,
            name: String,
            label: String,
            type: String,
            parent: String,
            enabled: null,
            selectTypes: false,
            types: Array,
            namesInUse: Array,
            errorAlreadyInUse: t`Name already in use`,
        };
    },

    mounted() {
        this.original = this.source || {};
        this.id = this.source?.id || '';
        this.name = this.source?.name || '';
        this.label = this.source?.label || '';
        this.type = this.source?.type || '';
        if (!this.type && this.source?.types) {
            this.selectTypes = true;
            this.namesInUse = [];
        } else {
            this.namesInUse = this.names || [];
        }
        this.enabled = this.source?.enabled || false;
        this.parent = this.source?.parent_id || '';
    },

    methods: {
        updateType() {
            this.namesInUse = this.allnames[this.type] || [];
        },
        unchanged() {
            const cat = {
                id: this.id,
                name: this.name,
                label: this.label,
                enabled: this.enabled || false,
                type: this.type,
                parent_id: this.parent || null
            };

            return cat.name === this.original.name && cat.label === this.original.label && cat.enabled === this.original.enabled && cat.parent_id === this.original.parent_id;
        },

        async onCreate() {
            const prefix = t`Error while creating new category`;
            await this.save(prefix);
        },

        async save(prefix) {
            try {
                const formData = new FormData();
                if (this.id) {
                    formData.set('id', this.id);
                }
                formData.set('name', this.name);
                formData.set('label', this.label);
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
            showError(`${prefix}: ${error}`);
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
