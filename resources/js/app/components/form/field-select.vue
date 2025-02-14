<template>
    <select
        :id="id"
        :name="name"
        :data-ref="reference"
        class="field-select"
        v-model="v"
        @change="change($event.target.value)"
    >
        <option
            v-for="item in items"
            :value="item"
            :key="item"
        >
            {{ item }}
        </option>
    </select>
</template>
<script>
export default {
    name: 'FieldSelect',
    props: {
        id: {
            type: String,
            required: true
        },
        items: {
            type: Array,
            required: true
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
            v: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.v = this.value;
        });
    },
    methods: {
        change(value) {
            this.$emit('change', value);
        },
    },
}
</script>
