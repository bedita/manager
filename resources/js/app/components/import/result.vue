<template>
    <div v-if="result">
        <div class="result">
            <h2>{{ msgResult }}</h2>
        </div>
        <fieldset id="import-result">
            <div class="import-message">
                <h2>{{ result?.info }}</h2>
                <p>{{ msgNumberCreated }}: {{ result?.created || '0' }}</p>
                <p>{{ msgNumberUpdated }}: {{ result?.updated || '0' }}</p>
                <p>{{ msgNumberErrors }}: {{ result?.errors || '0' }}</p>

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

    name: 'import-result',

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
}
</script>
