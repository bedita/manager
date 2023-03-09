<template>

    <div class="properties-container">

        <object-property v-for="(prop, index) in properties"
            :key="prop.id"
            :prop="prop"
            :type="type"
            :is-hidden="hidden.includes(prop.attributes?.name)"
            :is-new="prop.id == 0"
            :nobuttonsfor="immutable">
        </object-property>

        <p v-if="properties.length == 0">
            {{ t('No properties') }}
        </p>

        <div class="fieldset tab-container" v-if="type == 'custom'">
            <header class="unselectable">
                <h2>{{ t('Add custom property') }}</h2>
            </header>
            <div class="input text">
                <label>{{ t('Name') }}
                    <input type="text" :placeholder="t('Property name')" v-model="propName">
                </label>
            </div>
            <div class="input select">
                <label for="prop-type">{{ t('Type') }}
                    <select name="prop_type" v-model="propType">
                        <option v-for="(t,idx) in propTypes" :value="t.value" :key="idx">{{ t.text }}</option>
                    </select>
                </label>
            </div>

            <button @click.prevent="add">{{ t('Add') }}</button>
        </div>

    </div>

</template>
<script>

export default {
    props: {
        initProperties: [],
        type: '',
        hidden: [],
        propTypes: [],
    },

    components: {
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
    },

    data() {
        return {
            properties: [],
            propName: '',
            propType: '',
            immutable: ['created', 'id', 'lang', 'locked', 'modified', 'status', 'uname'],
        };
    },

    mounted() {
        this.properties = this.initProperties;
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
            this.propName = '';
            this.propType = '';
        },

        updateAdded() {
            let addedProperties = JSON.parse(document.getElementById('addedProperties').value || '[]') || [];
            addedProperties.push({
                name: this.propName,
                type: this.propType,
            });
            const newVal = JSON.stringify(addedProperties);
            document.getElementById('addedProperties').value = newVal;
        }
    },
}
</script>
