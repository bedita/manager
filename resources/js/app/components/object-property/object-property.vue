<template>
    <div :class="boxClass()" v-show="display">
        <div class="columns">
            <div class="column">
                <span :class="tagClass()">{{ prop.attributes.name }}</span>
                <p>{{ msgLabel }}: {{ prop.attributes.label || '-' }}</p>
                <p>{{ msgType }}: {{ prop.attributes.property_type_name }}</p>
                <p>{{ msgHidden }}:
                    <span v-if="hidden">{{ msgYes }}</span>
                    <span v-if="!hidden">{{ msgNo }}</span>
                </p>
                <p v-if="type === 'inherited'">
                    {{ msgInheritedFrom }}:
                    {{ prop.attributes.object_type_name }}
                </p>
                <p v-if="prop.attributes.description">&nbsp;</p>
                <p v-if="prop.attributes.description">{{ prop.attributes.description }}</p>
            </div>
            <div v-if="!nobuttonsfor.includes(prop.attributes.name)" class="column is-narrow">
                <div class="buttons-container">
                    <button v-if="type === 'custom'" @click.prevent="remove()" class="icon-cancel button button-outlined button-text-white is-expanded">{{ msgDelete }}</button>
                    <button v-if="hidden" @click.prevent="toggle(false)" class="icon-eye button button-outlined button-text-white is-expanded">{{ msgShow }}</button>
                    <button v-if="!hidden" @click.prevent="toggle(true)" class="icon-eye-off button button-outlined button-text-white is-expanded">{{ msgHide }}</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import { t } from 'ttag';

export default {
    props: {
        prop: [],
        nobuttonsfor: [],
        type: '',
        isHidden: false,
        isNew: false,
    },

    data() {
        return {
            confirm: null,
            hidden: false,
            display: true,
            // i18n @see https://github.com/ttag-org/ttag/issues/201
            msgDelete: t`Delete`,
            msgHidden: t`Hidden`,
            msgHide: t`Hide`,
            msgInheritedFrom: t`Inherited from`,
            msgLabel: t`Label`,
            msgNo: t`No`,
            msgShow: t`Show`,
            msgType: t`Type`,
            msgYes: t`Yes`,
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.hidden = this.isHidden || false;
        });
    },

    methods: {
        boxClass() {
            if (this.hidden) {
                return 'box pb-05 has-background-gray-600 has-text-white';
            }
            if (this.type === 'custom') {
                return 'box pb-05 has-background-info has-text-white';
            }
            if (this.type === 'inherited') {
                return 'box pb-05 has-background-gray-800 has-text-white';
            }

            return 'box pb-05 has-background-black has-text-white';
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
        toggle(val) {
            this.hidden = val;
            let hiddenProperties = JSON.parse(document.getElementById('hidden').value || '[]') || [];
            const index = hiddenProperties.indexOf(this.prop.attributes.name);
            if (index >= 0 && !this.hidden) {
                // remove property from hidden properties
                hiddenProperties.splice(index, 1);
                const newVal = JSON.stringify(hiddenProperties);
                document.getElementById('hidden').value = newVal;

                return;
            }
            if (index < 0 && this.hidden) {
                // add property to hidden properties
                hiddenProperties.push(this.prop.attributes.name);
                hiddenProperties.sort();
                const newVal = JSON.stringify(hiddenProperties);
                document.getElementById('hidden').value = newVal;
            }
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
