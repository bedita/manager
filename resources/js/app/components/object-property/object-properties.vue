<template>

    <div class="properties-container">

        <object-property v-for="(prop, index) in properties"
            :key="prop.id"
            :prop="prop"
            :type="type"
            :is-hidden="hidden.includes(prop.attributes?.name)"
            :is-new="prop.id == 0"
            :is-translatable="translatableProperties.includes(prop.attributes?.name)"
            :nobuttonsfor="immutable">
        </object-property>

        <p v-if="properties.length == 0">
            {{ t('No properties') }}
        </p>

    </div>

</template>
<script>

export default {
    props: {
        initProperties: [],
        type: '',
        hidden: [],
        translatable: [],
        translationRules: [],
    },

    components: {
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
    },

    data() {
        return {
            properties: [],
            translatableProperties: [],
            immutable: ['created', 'id', 'lang', 'locked', 'modified', 'status', 'uname'],
        };
    },

    mounted() {
        this.properties = this.initProperties;
        this.translatableProperties = this.translatable;
        if (this.translationRules) {
            this.translatableProperties = this.translatable.filter((prop) => {
                return !(prop in this.translationRules);
            });
            const forceTranslatable = Object.keys(this.translationRules).filter((prop) => {
                return this.translationRules[prop] === true;
            });
            this.translatableProperties = [...this.translatableProperties, ...forceTranslatable];
        }
        if (this.type !== 'custom') {
            return;
        }
        this.$eventBus.$on('prop-added', (item) => {
            this.properties.push({
                id: 0,
                attributes: item,
            });
        });
    },
}
</script>
