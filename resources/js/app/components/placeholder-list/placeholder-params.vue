<template>
    <div class="placeholderParams">
        <div v-for="column in Object.keys(parameters)"
             :key="column"
        >
            <span class="paramName">{{ t(column) }}</span>
            <template v-if="parameters[column] === 'integer'">
                <input type="number"
                       :placeholder="column"
                       v-model="decodedValue[column]"
                       @change="changeParams"
                >
            </template>
            <template v-if="parameters[column] === 'string'">
                <input type="text"
                       :placeholder="column"
                       v-model="decodedValue[column]"
                       @change="changeParams"
                >
            </template>
            <template v-if="parameters[column] === 'boolean'">
                <input type="checkbox"
                       v-model="decodedValue[column]"
                       @change="changeParams"
                >
            </template>
            <template v-if="parameters[column] === 'richtext'">
                <field-textarea
                    :id="`${column}-${Math.random().toString(36)}`"
                    :name="column"
                    :field="column"
                    :value="decodedValue[column]"
                    @change="(value) => changeRichText(value, column)"
                />
            </template>
            <template v-if="typeof parameters[column] === 'object' ">
                <div>
                    <select v-model="decodedValue[column]"
                            @change="changeParams"
                    >
                        <option v-for="option in parameters[column]"
                                :key="option"
                                :value="option"
                        >
                            {{ t(option) }}
                        </option>
                    </select>
                </div>
            </template>
        </div>
    </div>
</template>
<script>
import { PlaceholderBus as placeholderBus } from 'app/components/placeholder-bus';

export default {
    name: 'PlaceholderParams',
    props: {
        field: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        text: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        value: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            parameters: [],
            newValue: null,
            oldValue: null,
            decodedValue: {},
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.parameters = BEDITA?.placeholdersConfig?.[this.type] || {};
            const decoded = this.$helpers.base64ToUtf8(this.value);
            if (decoded === 'undefined' || decoded === '') {
                this.newValue = this.$helpers.utf8ToBase64('undefined');

                return;
            }
            try {
                this.decodedValue = JSON.parse(decoded);
            } catch(e) {
                console.error(e, decoded, this.value);
            }
        });
    },
    methods: {
        changeParams() {
            this.oldValue = this.newValue || this.value;
            this.newValue = this.$helpers.utf8ToBase64(JSON.stringify(this.decodedValue));
            placeholderBus.send('replace-placeholder', {
                id: this.id,
                field: this.field,
                oldParams: this.oldValue,
                newParams: this.newValue,
            });
            this.oldValue = this.newValue;
        },
        changeParamsBoolean(value, column) {
            this.decodedValue[column] = value;
            this.changeParams();
        },
        changeRichText(value, column) {
            this.decodedValue[column] = value;
            this.changeParams();
        },
    },
};
</script>
<style>
div.placeholderParams {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 4px 0;
}

.paramName {
    display: block;
    margin-bottom: 4px;
    text-transform: capitalize;
}
</style>
