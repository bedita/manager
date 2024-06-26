<template>
    <div :class="boxClass" v-show="display">
        <div class="columns">
            <div class="column">
                <span :class="tagClass">
                    {{ prop.attributes.name }}
                    <i v-if="prop.attributes.description" v-title="prop.attributes.description" class="ml-05">
                        <app-icon icon="carbon:information"></app-icon>
                    </i>
                </span>
                <table>
                    <tr>
                        <td class="column-header">{{ msgLabel }}</td>
                        <td>{{ prop.attributes.label }}</td>
                    </tr>
                    <tr>
                        <td class="column-header">{{ msgType }}</td>
                        <td class="property-val">{{ prop.attributes.property_type_name }}</td>
                    </tr>
                    <tr>
                        <td class="column-header">{{ msgReadonly }}</td>
                        <td class="property-val">{{ readonly }}</td>
                    </tr>
                    <tr>
                        <td class="column-header">{{ msgHidden }}</td>
                        <td>
                            <select v-model="hidden" @change="updateHidden()">
                                <option :value="true">{{ msgYes }}</option>
                                <option :value="false">{{ msgNo }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="column-header">{{ msgTranslatable }}</td>
                        <td>
                            <select v-model="translatable" @change="updateTranslationRules()">
                                <option value="true">{{ msgYes }}</option>
                                <option value="false">{{ msgNo }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="type === 'inherited'">
                        <td v-if="type === 'inherited'" class="column-header">{{ msgInheritedFrom }}</td>
                        <td v-if="type === 'inherited'">{{ prop.attributes.object_type_name }}</td>
                    </tr>
                </table>
            </div>
            <div v-if="!nobuttonsfor.includes(prop.attributes.name)" class="column is-narrow">
                <div class="buttons-container">
                    <button v-if="type === 'custom'" @click.prevent="remove()" class="icon-cancel button button-outlined button-text-white is-expanded">{{ msgDelete }}</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import { t } from 'ttag';

export default {
    props: {
        prop: {
            type: Object,
            default: () => ({}),
        },
        nobuttonsfor: {
            type: Array,
            default: () => ([]),
        },
        type: {
            type: String,
            default: '',
        },
        isHidden: {
            type: Boolean,
            default: false,
        },
        isNew: {
            type: Boolean,
            default: false,
        },
        isTranslatable: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            confirm: null,
            hidden: false,
            translatable: false,
            display: true,
            readonly: false,
            msgDelete: t`Delete`,
            msgHidden: t`Hidden`,
            msgHide: t`Hide`,
            msgInheritedFrom: t`Inherited from`,
            msgLabel: t`Label`,
            msgNo: t`No`,
            msgReadonly: t`Readonly`,
            msgShow: t`Show`,
            msgTranslatable: t`Translatable`,
            msgType: t`Type`,
            msgYes: t`Yes`,
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.hidden = this.isHidden || false;
            this.readonly = this.prop.attributes.read_only || false;
            this.translatable = this.isTranslatable || false;
        });
    },
    computed: {
        boxClass() {
            if (this.hidden === true) {
                return 'object-property-container box pb-05 has-background-gray-600 has-text-white';
            }
            if (this.type === 'custom') {
                return 'object-property-container box pb-05 has-background-info has-text-white';
            }
            if (this.type === 'inherited') {
                return 'object-property-container box pb-05 has-background-gray-800 has-text-white';
            }

            return 'object-property-container box pb-05 has-background-black has-text-white';
        },
        tagClass() {
            if (this.type === 'custom') {
                return 'tag is-dark';
            }
            if (this.type === 'inherited') {
                return `tag has-background-module-${this.prop.attributes.object_type_name}`;
            }

            return 'tag';
        },
    },

    methods: {
        updateHidden() {
            let hiddenProperties = JSON.parse(document.getElementById('hidden').value || '[]') || [];
            const index = hiddenProperties.indexOf(this.prop.attributes.name);
            if (index >= 0 && this.hidden === false) {
                // remove property from hidden properties
                hiddenProperties.splice(index, 1);
                const newVal = JSON.stringify(hiddenProperties);
                document.getElementById('hidden').value = newVal;

                return;
            }
            if (index < 0 && this.hidden === true) {
                // add property to hidden properties
                hiddenProperties.push(this.prop.attributes.name);
                hiddenProperties.sort();
                const newVal = JSON.stringify(hiddenProperties);
                document.getElementById('hidden').value = newVal;
            }
        },
        updateTranslationRules() {
            let translationRules = JSON.parse(document.getElementById('translationRules').value || '{}') || {};
            translationRules[this.prop.attributes.name] = this.translatable === 'true';
            document.getElementById('translationRules').value = JSON.stringify(translationRules);
        },
        remove() {
            this.confirm = BEDITA.confirm(
                t`If you confirm, this resource will be gone forever. Are you sure?`,
                t`yes, proceed`,
                () => {
                    this.delete();
                }
            );
        },
        async delete() {
            if (this.isNew) {
                this.display = false;

                return;
            }
            const prefix = t`Error on deleting property`;
            try {
                const options = {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                };
                const response = await fetch(`${BEDITA.base}/api/model/properties/${this.prop.id}`, options);
                if (response.status === 200) {
                    this.display = false;
                } else if (response.error) {
                    BEDITA.showError(`${prefix}: ${response.error}`);
                    this.handleError(response.error, prefix);
                }
            } catch (error) {
                BEDITA.showError(`${prefix}: ${error}`);
            } finally {
                this.confirm.hide();
            }
        },
    },
}
</script>
<style>
div.object-property-container {
    padding: 0.5rem;
    margin: 0.5rem 0;
    border-radius: 25px;
    border: 2px solid cyan;
}
div.object-property-container > div.columns > div.column > p {
    padding: 0.5rem 0.5rem 0.5rem 0;
    margin: 0.5rem 0;
    border-bottom: dashed 1px gray;
}
div.object-property-container > div.columns > div.column > div > span {
    padding: 4px 8px;
    border-left: 1px dotted black;
    border-bottom: 1px dotted black;
    white-space: normal;
    vertical-align: middle;
}
div.object-property-container > div.columns > div.column > div > span > a:hover {
    text-decoration: underline;
}
div.object-property-container > div.columns > div.column > div.rows > div {
    border-bottom: solid 1px gray;
    padding: 0.5rem 0;
}
div.object-property-container > div.columns > div.column td.column-header {
    color: yellow;
    text-align: left;
}
div.object-property-container > div.columns > div.column td.property-val {
    font-family: monospace;
}
</style>
