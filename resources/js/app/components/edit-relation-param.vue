<template>
    <div>
        <span
            :title="propertyKey"
        >
            {{ property?.description || propertyKey }}
        </span>

        <!-- Boolean --->
        <div v-if="propertyType == 'boolean'">
            <input
                type="checkbox"
                :checked="!!value"
                @change="update($event.target.checked)"
            >
        </div>

        <!-- String (date, enum, string, text) --->
        <div
            :class="{ datepicker: ['date', 'date-time'].includes(propertyFormat) }"
            v-if="propertyType == 'string'"
        >
            <input
                date="true"
                :time="propertyFormat == 'date-time'"
                :value="value"
                v-datepicker
                @change="update($event.target.value)"
                v-if="['date', 'date-time'].includes(propertyFormat)"
            >
            <select
                :value="value"
                @change="update($event.target.value)"
                v-else-if="isEnum"
            >
                <option
                    :value="item"
                    v-for="item in property.enum"
                    :key="item"
                >
                    {{ item }}
                </option>
            </select>
            <input
                type="text"
                :value="value"
                @change="update($event.target.value)"
                v-else
            >
        </div>
        <div v-if="propertyType == 'text'">
            <textarea
                :value="value"
                @change="update($event.target.value)"
            ></textarea>
        </div>

        <!-- Number --->
        <div v-if="propertyType == 'number'">
            <input
                type="number"
                :name="propertyKey"
                step="any"
                :value="value"
                @change="update($event.target.value)"
            >
        </div>

        <!-- Integer --->
        <div v-if="propertyType == 'integer'">
            <input
                type="number"
                :name="propertyKey"
                :value="value"
                @change="update($event.target.value)"
            >
        </div>
    </div>
</template>
<script>
export default {
    name: 'PropertyField',
    props: {
        property: {
            type: Object,
            required: true,
        },
        propertyKey: {
            type: String,
            default: '',
        },
        value: {
            type: [String, Number, Boolean],
            default: null,
        },
    },

    computed: {
        propertyType() {
            let type = this.property.type || null;
            if (this.property.oneOf) {
                const firstNotNull = this.property.oneOf.find(p => p.type !== 'null');
                type = firstNotNull.type || type || null;
            }

            return type;
        },

        propertyFormat() {
            let format = this.property.format || null;
            if (this.property.oneOf) {
                const firstNotNull = this.property.oneOf.find(p => p.type !== 'null');
                format = firstNotNull.format || format || null;
            }

            return format;
        },

        isEnum() {
            let type = !!this.property.enum || false;
            if (this.property.oneOf) {
                const firstNotNull = this.property.oneOf.find(p => p.type !== 'null');
                type = !!firstNotNull.enum || type || false;
            }

            return type;
        },

    },

    methods: {
        update(value) {
            this.$emit('input', value);
        }
    },
}
</script>
