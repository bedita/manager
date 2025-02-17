<template>
    <div class="field-date datepicker">
        <input
            :id="id"
            :name="name"
            :data-ref="reference"
            :time="time"
            :value="value"
            type="text"
            date="true"
            v-datepicker
            @change="change($event.target.value)"
        >
    </div>
</template>
<script>
export default {
    name: 'FieldDate',
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
        time: {
            type: Boolean,
            default: false
        },
        value: {
            type: String,
            default: ''
        },
    },
    methods: {
        change(value) {
            this.$emit('change', value);
        }
    },
}
</script>
