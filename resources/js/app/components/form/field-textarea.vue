<template>
    <textarea
        :id="id"
        :name="name"
        :data-ref="reference"
        :data-config="config"
        :data-toolbar="toolbar"
        class="field-textarea"
        v-model="v"
        v-richeditor
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
            config: null,
            loaded: false,
            toolbar: null,
            v: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            const map = BEDITA?.richeditorByPropertyConfig?.[this.field] || null;
            if (map) {
                map.config = map.config || {};
                map.toolbar = map.toolbar || {};
            }
            this.config = JSON.stringify(map?.config) || null;
            this.toolbar = JSON.stringify(map?.toolbar) || null;
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
