<template>
    <div class="mt-25">
        <h3>{{ t('Create') }}</h3>
        <input type="text" :placeholder="t('Name')" v-model="propName" />
        <select v-model="propType">
            <option v-for="(t,idx) in propTypes" :value="t.value" :key="idx">{{ t.text }}</option>
        </select>
        <input type="text" v-model="propDescription" :placeholder="t('Description')" />
        <button @click.prevent="add" :disabled="!propName || !propType">{{ t('Add') }}</button>
    </div>
</template>

<script>

export default {

    props: {
        propTypes: [],
    },

    data() {
        return {
            propDescription: '',
            propName: '',
            propType: 'string',
        };
    },

    methods: {
        add() {
            this.updateAdded();
            this.$eventBus.$emit('prop-added', {
                name: this.propName,
                property_type_name: this.propType,
                description: this.propDescription,
            });
            this.propDescription = '';
            this.propName = '';
            this.propType = '';
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
