<template>
    <div class="placeholderParams">
        <div v-for="column in Object.keys(parameters)">
            <span v-if="['boolean', 'integer', 'string'].includes(parameters[column])">{{ t(column) }}</span>
            <template v-if="parameters[column] === 'integer'">
                <input type="number" :placeholder="column" v-model="decodedValue[column]" @change="changeParams" />
            </template>
            <template v-if="parameters[column] === 'string'">
                <input type="text" :placeholder="column" v-model="decodedValue[column]" @change="changeParams" />
            </template>
            <template v-if="parameters[column] === 'boolean'">
                <input type="checkbox" v-model="decodedValue[column]" @click="changeParams" />
            </template>
        </div>
    </div>
</template>
<script>
import { EventBus } from 'app/components/event-bus';

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
            const decoded = this.decoded(this.value);
            if (decoded === 'undefined') {
                this.newValue = btoa('undefined');

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
            this.newValue = btoa(JSON.stringify(this.decodedValue));
            EventBus.send('replace-placeholder', {
                id: this.id,
                field: this.field,
                oldParams: this.oldValue,
                newParams: this.newValue,
            });
            this.oldValue = this.newValue;
        },
        decoded(item) {
            return atob(item);
        },
    },
};
</script>
<style>
div.placeholderParams {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    text-align: center;
    align-items: center;
    gap: 8px;
    margin: 4px 0;
}
</style>
