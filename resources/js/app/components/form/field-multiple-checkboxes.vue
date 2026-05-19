<template>
    <div class="field-multiple-checkboxes">
        <label
            v-for="item in items"
            :key="item"
        >
            <input
                :id="`field-multiple-checkboxes-${item}`"
                :name="item"
                :data-ref="reference"
                class="field-checkbox"
                type="checkbox"
                :checked="v.includes(item)"
                @change="update($event.target)"
            >
            <span>{{ item }}</span>
        </label>
        <input
            :id="id"
            :name="name"
            :data-ref="reference"
            type="hidden"
            v-model="serialized"
            @change="$emit('change', serialized)"
        >
    </div>
</template>
<script>
export default {
    name: 'FieldMultipleCheckboxes',
    props: {
        id: {
            type: String,
            required: true
        },
        jsonSchema: {
            type: Object,
            required: true,
        },
        name: {
            type: String,
            required: true
        },
        reference: {
            type: String,
            default: ''
        },
        value: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            items: this.jsonSchema?.items?.enum || [],
            serialized: JSON.stringify(this.value || []),
            v: this.value || [],
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.items.sort();
            this.v.sort();
        });
    },
    methods: {
        update(target) {
            const val = target.checked;
            const targetName = target.name;
            if (val) {
                this.v.push(targetName);
            } else {
                this.v = this.v.filter(i => i !== targetName);
            }
            this.v.sort();
            this.serialized = JSON.stringify(this.v);
            this.$emit('change', this.serialized);
        }
    },
}
</script>
<style scoped>
div.field-multiple-checkboxes {
    display: flex;
    flex-direction: column;
}
div.field-multiple-checkboxes > label {
    cursor: pointer;
}
div.field-multiple-checkboxes > label:hover {
    text-decoration: underline;
}
</style>
