<template>
    <div v-if="result">
        <div class="result">
            <h2 v-if="!allZero">{{ msgResult }}</h2>
        </div>
        <fieldset id="import-result">
            <div class="import-message">
                <h2 v-html="result?.info"></h2>
                <p v-if="!allZero">{{ msgNumberCreated }}: {{ result?.created || '0' }}</p>
                <p v-if="!allZero">{{ msgNumberUpdated }}: {{ result?.updated || '0' }}</p>
                <p v-if="!allZero">{{ msgNumberErrors }}: {{ result?.errors || '0' }}</p>

                <div class="import-message import-warn" v-if="result.warn">
                    <h2>{{ msgImportWarn }}</h2>
                    <div>{{ result?.warn }}</div>
                </div>

                <div class="import-message import-error" v-if="result.error">
                    <h2>{{ msgImportError }}</h2>
                    <div>{{ result?.error }}</div>
                </div>
            </div>
        </fieldset>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {

    name: 'ImportResult',

    props: {
        result: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            msgImportError: t`Import error`,
            msgImportWarn: t`Import warn`,
            msgNumberCreated: t`Number of created resources`,
            msgNumberUpdated: t`Number of updated resources`,
            msgNumberErrors: t`Number of errors`,
            msgResult: t`Result`,
        };
    },

    computed: {
        allZero() {
            const created = this.result?.created || 0;
            const updated = this.result?.updated || 0;
            const errors = this.result?.errors || 0;

            return created === updated && updated === errors && errors === 0;
        },
    },
}
</script>
