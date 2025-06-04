<template>
    <div class="date-range-panel">
        <template v-if="showPanel">
            <div
                class="backdrop"
                style="display: block; z-index: 9998;"
                @click="closePanel()"
            />
            <aside
                class="main-panel-container on"
                custom-footer="true"
                custom-header="true"
            >
                <div class="main-panel fieldset">
                    <header class="mx-1 mt-1 tab tab-static unselectable">
                        <h2>{{ msgAddMultipleDateRanges }}</h2>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click.prevent.stop="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div class="pcontainer">
                        <div class="form-field">
                            <div class="fields">
                                <label><span>{{ msgDate }}</span> <span class="required">*</span></label>
                                <label><span>{{ msgFrom }}</span> <span class="required">*</span></label>
                                <label><span>{{ msgTo }}</span> <span class="required">*</span></label>
                                <label>{{ msgRepeatType }} <span class="required">*</span></label>
                                <label>{{ msgRepeatTimes }} <span class="required">*</span></label>
                                <input
                                    type="date"
                                    v-model="start"
                                >
                                <input
                                    type="time"
                                    v-model="from"
                                >
                                <input
                                    type="time"
                                    v-model="to"
                                >
                                <select
                                    required
                                    v-model="rangeType"
                                >
                                    <option value="daily">{{ msgDay }}</option>
                                    <option value="weekly">{{ msgWeek }}</option>
                                    <option value="monthly">{{ msgMonth }}</option>
                                </select>
                                <input
                                    type="number"
                                    min="1"
                                    max="100"
                                    class="input input-text input-range-number"
                                    required
                                    v-model="numberOfRanges"
                                >
                            </div>
                        </div>
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                :disabled="addDisabled"
                                @click="add"
                            >
                                <app-icon icon="carbon:save" />
                                <span class="ml-05">
                                    {{ msgAdd }}
                                </span>
                            </button>
                            <button
                                class="button button-primary"
                                @click.prevent.stop="closePanel()"
                            >
                                <app-icon icon="carbon:close" />
                                <span class="ml-05">
                                    {{ msgClose }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </template>
    </div>
</template>
<script>
import { t } from 'ttag';
export default {
    name: 'DateRangePanel',
    props: {
        showPanel: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            from: '',
            to: '',
            msgAdd: t`Add`,
            msgAddMultipleDateRanges: t`Add multiple date ranges`,
            msgClose: t`Close`,
            msgDate: t`Date`,
            msgDay: t`Day`,
            msgFrom: t`From`,
            msgMonth: t`Month`,
            msgTo: t`To`,
            msgRepeatTimes: t`Repeat times`,
            msgRepeatType: t`Repeat type`,
            msgRequired: t`Required`,
            msgWeek: t`Week`,
            numberOfRanges: 1,
            rangeType: 'daily', // default to daily
            start: '', // Start date for the range
        }
    },
    computed: {
        addDisabled() {
            return !this.start || !this.from || !this.to || !this.rangeType || this.numberOfRanges < 1 || this.numberOfRanges > 100;
        },
    },
    methods: {
        add() {
            for (let i = 0; i < this.numberOfRanges; i++) {
                const startDate = new Date(this.start + 'T' + this.from);
                const endDate = new Date(this.start + 'T' + this.to);
                if (this.rangeType === 'daily') {
                    startDate.setDate(startDate.getDate() + i);
                    endDate.setDate(endDate.getDate() + i);
                } else if (this.rangeType === 'weekly') {
                    startDate.setDate(startDate.getDate() + (i * 7));
                    endDate.setDate(endDate.getDate() + (i * 7));
                } else if (this.rangeType === 'monthly') {
                    startDate.setMonth(startDate.getMonth() + i);
                    endDate.setMonth(endDate.getMonth() + i);
                }
                this.$emit('add-range', { start: startDate, end: endDate });
            }
            this.closePanel();
        },
        closePanel() {
            this.$emit('update:showPanel', false);
        },
    },
}
</script>
<style scoped>
div.date-range-panel aside.main-panel-container {
    z-index: 9999;
}
div.date-range-panel aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
div.date-range-panel button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
div.date-range-panel .pcontainer {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
div.date-range-panel .pcontainer > div {
    display: flex;
    flex-direction: column;
}
div.date-range-panel div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
}
div.date-range-panel span.required {
    color: red;
}
div.date-range-panel div.root > label {
    display:flex;
    align-items:center;
    direction:column;
    cursor: pointer;
}
div.date-range-panel div.root > label > span {
    display: flex;
    align-self: center;
    cursor: pointer;
}
div.date-range-panel input.input-range-number {
    max-width: 120px;
}
div.date-range-panel .fields {
    display: grid;
    grid-template-columns: 150px 100px 100px 200px 200px;
    column-gap: 0.5rem;
}
</style>
