<template>
    <div class="object-property-add">
        <h3>{{ t('Create') }}</h3>
        <input
            type="text"
            :placeholder="t('Name')"
            v-model="propName"
        >
        <select v-model="propType">
            <option
                v-for="(item, idx) in propTypes"
                :value="getPropTypeValue(item)"
                :key="idx"
            >
                {{ getPropTypeText(item) }}
            </option>
        </select>
        <input
            type="text"
            :placeholder="t('Description')"
            v-model="propDescription"
        >
        <button
            :disabled="!propName || !propType"
            @click.prevent="add"
        >
            {{ t('Add') }}
        </button>
    </div>
</template>

<script>
export default {
    name: 'ObjectPropertyAdd',

    props: {
        propTypes: {
            type: Array,
            default: () => ([]),
        },
    },

    emits: ['prop-added'],

    data() {
        return {
            propDescription: '',
            propName: '',
            propType: 'string',
        };
    },

    methods: {
        getPropTypeText(item) {
            if (!item) {
                return '';
            }

            if (typeof item === 'string') {
                return item;
            }

            return item.text || item.label || item.value || '';
        },

        getPropTypeValue(item) {
            if (!item) {
                return '';
            }

            if (typeof item === 'string') {
                return item;
            }

            return item.value || item.text || item.label || '';
        },

        add() {
            this.updateAdded();
            this.$emit('prop-added', {
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
<style scoped>
.object-property-add {
    margin-top: 1rem;
}
</style>
