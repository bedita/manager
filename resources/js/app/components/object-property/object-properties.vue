<template>
    <div class="properties-container">
        <object-property v-for="prop in properties"
            :key="prop.id"
            :prop="prop"
            :type="type"
            :is-hidden="hidden.includes(prop.attributes?.name)"
            :is-new="prop.id == 0"
            :is-translatable="translatable.includes(prop.attributes?.name)"
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
        initProperties: {
            type: Array,
            default: () => ([]),
        },
        type: {
            type: String,
            default: '',
        },
        hidden: {
            type: Array,
            default: () => ([]),
        },
        translatable: {
            type: Array,
            default: () => ([]),
        },
        translationRules: {
            type: Array,
            default: () => ([]),
        },
    },

    components: {
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
    },

    data() {
        return {
            properties: [],
            immutable: ['created', 'id', 'lang', 'locked', 'modified', 'status', 'uname'],
        };
    },

    mounted() {
        this.properties = this.initProperties;
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
