<template>
    <div class="field-radio">
        <label
            v-for="item in items"
            :key="item"
        >
            <input
                :id="id"
                :name="name"
                :data-ref="reference"
                :value="item"
                type="radio"
                v-model="v"
                @change="change($event.target.value)"
            >
            {{ item }}
        </label>
    </div>
</template>
<script>
export default {
    name: 'FieldRadio',
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
        }
    },
}
</script>
<style scoped>
.field-radio {
    display: flex;
    flex-direction: row;
    width: fit-content;
}
.field-radio > label {
    flex: auto !important;
}
</style>
