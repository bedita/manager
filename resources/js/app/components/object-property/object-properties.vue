<template>
    <div class="object-properties">
        <div class="properties-container">
            <object-property
                v-for="prop in properties"
                :key="prop.id"
                :prop="prop"
                :type="type"
                :is-hidden="hidden.includes(prop.attributes?.name)"
                :is-new="prop.id < 0"
                :is-translatable="translatable.includes(prop.attributes?.name)"
                :nobuttonsfor="immutable"
            />
        </div>

        <p v-if="properties.length == 0">
            {{ t('No properties') }}
        </p>

        <slot :on-prop-added="onPropAdded" />
    </div>
</template>
<script>

export default {
    components: {
        ObjectProperty: () => import(/* webpackChunkName: "object-property" */'app/components/object-property/object-property'),
    },

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

    emits: ['prop-added'],

    data() {
        return {
            properties: [],
            immutable: ['created', 'id', 'lang', 'locked', 'modified', 'status', 'uname'],
            newPropertyId: -1,
        };
    },

    mounted() {
        this.properties = this.initProperties;
    },

    methods: {
        onPropAdded(item) {
            if (this.type !== 'custom') {
                return;
            }
            this.properties.push({
                id: this.newPropertyId--,
                attributes: item,
            });
            // Re-emit the event so parent components can also listen
            this.$emit('prop-added', item);
        }
    }
}
</script>
<style scoped>
.object-properties {
    margin-top: 1rem;
}
</style>
