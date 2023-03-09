<template>
    <div class="mt-25" v-if="type == 'custom'">
        <h3>{{ t('Create') }}</h3>
        <input id="newPropName" type="text" :placeholder="t('Property name')" v-model="propName" />
        <select id="newPropType" name="prop_type" v-model="propType">
            <option v-for="(t,idx) in propTypes" :value="t.value" :key="idx">{{ t.text }}</option>
        </select>
        <input type="text" v-model="propDescription" />
        <button @click.prevent="add" :disabled="!propName || !propType">{{ t('Add') }}</button>
    </div>
</template>

<script>

export default {

    props: {
        propTypes: [],
        type: '',
    },

    data() {
        return {
            propDescription: '',
            propName: '',
            propType: 'string',
            properties: [],
        };
    },

    methods: {
        add() {
            if (!this.propName || !this.propType) {
                return;
            }
            this.properties.push({
                id: 0,
                attributes: {
                    name: this.propName,
                    property_type_name: this.propType,
                },
            });
            this.updateAdded();
            this.propDescription = '';
            this.propName = '';
            this.propType = '';
            const button = document.querySelector('button[form=form-main]');
            button.classList.add('is-loading-spinner');
            button.click();
        },

        updateAdded() {
            let addedProperties = JSON.parse(document.getElementById('addedProperties').value || '[]') || [];
            addedProperties.push({
                description: this.propDescription,
                name: this.propName,
                type: this.propType,
            });
            const newVal = JSON.stringify(addedProperties);
            document.getElementById('addedProperties').value = newVal;
        }
    },
}
</script>
