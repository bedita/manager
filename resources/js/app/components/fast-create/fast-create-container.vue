<template>
    <div class="fast-create-container">
        <fast-create
            :auto-type="autoType"
            :fields-by-type="fieldsByType"
            :items="items"
            :languages="languages"
            :relation-label="relationLabel"
            :relation-name="relationName"
            :schemas-by-type="schemasByType"
            @created="created"
            v-if="items.length"
        />
    </div>
</template>
<script>
export default {
    name: 'FastCreateContainer',
    components: {
        FastCreate: () => import(/* webpackChunkName: "fast-create" */'app/components/fast-create/fast-create'),
    },
    props: {
        allowedTypes: {
            type: Array,
            default: () => [],
        },
        fieldsByType: {
            type: Object,
            required: true,
        },
        languages: {
            type: Object,
            default: () => {},
        },
        relationLabel: {
            type: String,
            required: true,
        },
        relationName: {
            type: String,
            required: true,
        },
        relationsSchema: {
            type: Object,
            required: true,
        },
        schemasByType: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            autoType: null,
            items: [],
            relationSchema: {},
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.relationSchema = this.relationsSchema[this.relationName] || {};
            this.autoType = this.relationSchema?.right?.length === 1 ? this.relationSchema.right[0] : null;
            this.items = (this.relationSchema?.right || []).filter(
                (item) => this.allowedTypes.includes(item)
            );
        });
    },
    methods: {
        created(item) {
            this.$emit('created', item);
        },
    },
};
</script>
