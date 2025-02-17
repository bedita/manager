<template>
    <textarea
        :id="id"
        :name="name"
        :data-ref="reference"
        class="field-json json"
        v-model="v"
        v-jsoneditor
        @change="change($event.target.value)"
    />
</template>
<script>
export default {
    name: 'FieldJson',
    props: {
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
            try {
                const parsed = JSON.parse(value);
                this.$emit('change', parsed);
            } catch (e) {
                this.$emit('error', e);
            }
        },
    },
}
</script>
