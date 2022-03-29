export default {
    template: `
    <div>
        <label :title="propertyKey"><: property.description || propertyKey :></label>

        <!-- Boolean --->
        <div v-if="propertyType == 'boolean'">
            <input type="checkbox" :checked="!!value" @change="update($event.target.checked)">
        </div>

        <!-- String (date, enum, text) --->
        <div v-if="propertyType == 'string'"
            :class="{ datepicker: propertyFormat == 'date-time' }">

            <input v-if="propertyFormat == 'date-time'"
                v-datepicker time="true" :value="value" @change="update($event.target.value)"></input>

            <select v-else-if="isEnum" :value="value" @change="update($event.target.value)">
                <option v-for="item in property.enum" :value="item"><: item :></option>
            </select>

            <input v-else type="text" :value="value" @change="update($event.target.value)">
        </div>

        <!-- Number --->
        <div v-if="propertyType == 'number'">
            <input type="number" :name="propertyKey" step="any" :value="value" @change="update($event.target.value)">
        </div>

        <!-- Integer --->
        <div v-if="propertyType == 'integer'">
            <input type="number" :name="propertyKey" :value="value" @change="update($event.target.value)">
        </div>
    </div>
    `,

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
