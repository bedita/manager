import { t } from 'ttag';

export default {
    template: `<div :class="boxClass()">
        <div class="columns">
            <div class="column">
                <span :class="tagClass()"><: prop.attributes.name :></span>
                <p>${t`Label`}: <: prop.attributes.label || '-' :></p>
                <p>${t`Type`}: <: prop.attributes.property_type_name :></p>
                <p>${t`Hidden`}:
                    <span v-if="hidden">${t`Yes`}</span>
                    <span v-if="!hidden">${t`No`}</span>
                </p>
                <p v-if="type === 'inherited'">
                    ${t`Inherited from`}:
                    <: prop.attributes.object_type_name :>
                </p>
                <p v-if="prop.attributes.description">&nbsp;</p>
                <p v-if="prop.attributes.description"><: prop.attributes.description :></p>
            </div>
            <div v-if="!(prop.attributes.name in nobuttonsfor)" class="column is-narrow">
                <div class="buttons-container">
                    <button v-if="type === 'custom'" @click.prevent="remove()" class="button is-expanded">${t`Delete`}</button>
                    <button v-if="hidden" @click.prevent="toggle(false)" class="button is-expanded">${t`Show`}</button>
                    <button v-if="!hidden" @click.prevent="toggle(true)" class="button is-expanded">${t`Hide`}</button>
                </div>
            </div>
        </div>
    </div>`,

    props: {
        prop: [],
        nobuttonsfor: [],
        type: '',
        ishidden: false,
    },

    data() {
        return {
            hidden: false,
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.hidden = this.ishidden || false;
        });
    },

    methods: {
        boxClass() {
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
                document.getElementById('hidden').setAttribute('data-original-value', newVal);

                return;
            }
            if (index < 0 && this.hidden) {
                // add property to hidden properties
                hiddenProperties.push(this.prop.attributes.name);
                hiddenProperties.sort();
                const newVal = JSON.stringify(hiddenProperties);
                document.getElementById('hidden').value = newVal;
                document.getElementById('hidden').setAttribute('data-original-value', newVal);
            }
        },
        remove() {
            BEDITA.confirm(
                t`If you confirm, this resource will be gone forever. Are you sure?`,
                t`yes, proceed`,
                () => {
                    this.delete();
                }
            );
        },
        async delete() {
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
                    document.location.reload();

                    return;
                }
                if (response.error) {
                    BEDITA.showError(`${prefix}: ${response.error}`);
                    this.handleError(response.error, prefix);
                }
            } catch (error) {
                BEDITA.showError(`${prefix}: ${error}`);
            }
        },
    },
}
