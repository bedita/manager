<template>
    <textarea
        :id="id"
        :name="name"
        :data-ref="reference"
        :data-loaded="loaded"
        class="field-textarea"
        v-model="v"
        v-richeditor="richeditorConfig"
        @change="change($event.target.value)"
        v-if="loaded"
    />
</template>
<script>
export default {
    name: 'FieldTextarea',
    props: {
        field: {
            type: String,
            required: true
        },
        id: {
            type: String,
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
            richeditorConfig: {},
            loaded: false,
            v: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.richeditorConfig = {
                config: BEDITA?.richeditorByPropertyConfig?.[this.field]?.config || {},
                toolbar: BEDITA?.richeditorByPropertyConfig?.[this.field]?.toolbar || null
            }
            this.v = this.value;
            this.loaded = true;
            this.$forceUpdate();
        });
    },
    methods: {
        change(value) {
            this.$emit('change', value);
        },
    },
}
</script>
