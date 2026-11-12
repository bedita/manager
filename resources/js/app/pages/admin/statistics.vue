<template>
    <div class="admin-statistics">
        <h2>{{ msgTitle }}</h2>
        <div>
            <!-- object type -->
            <div class="filter-field">
                <label for="statistics-object-type">{{ msgObjectType }}</label>
                <select
                    id="statistics-object-type"
                    v-model="typeChoice"
                    @change="changeType"
                >
                    <option
                        v-for="item,key in objectTypesChoices"
                        :key="key"
                        :value="key"
                    >
                        {{ item.label || item.name }}
                    </option>
                </select>
            </div>

            <!-- year -->
            <div class="filter-field">
                <label for="statistics-year">{{ msgYear }}</label>
                <select
                    id="statistics-year"
                    v-model="yearChoice"
                    @change="changeYear"
                >
                    <option
                        v-for="item in yearChoices"
                        :key="item"
                        :value="item"
                    >
                        {{ item }}
                    </option>
                </select>
            </div>

            <!-- month -->
            <div class="filter-field">
                <label for="statistics-month">{{ msgMonth }}</label>
                <select
                    id="statistics-month"
                    v-model="monthChoice"
                    @change="changeMonth"
                >
                    <option
                        v-for="item in monthChoices"
                        :key="item.value"
                        :value="item.value"
                    >
                        {{ item.label }}
                    </option>
                </select>
            </div>

            <!-- week -->
            <div class="filter-field">
                <label for="statistics-week">{{ msgWeek }}</label>
                <select
                    id="statistics-week"
                    :disabled="monthChoice == '-'"
                    v-model="weekChoice"
                    @change="changeWeek"
                >
                    <option
                        v-for="item in weekChoices"
                        :key="item.value"
                        :value="item.value"
                    >
                        {{ item.label }}
                    </option>
                </select>
            </div>

            <!-- day -->
            <div class="filter-field">
                <label for="statistics-day">{{ msgDay }}</label>
                <select
                    id="statistics-day"
                    :disabled="weekChoice == '-'"
                    v-model="dayChoice"
                    @change="changeDay"
                >
                    <option
                        v-for="item in dayChoices"
                        :key="item.value"
                        :value="item.value"
                    >
                        {{ item.label }}
                    </option>
                </select>
            </div>

            <div
                class="is-loading-spinner mt-05"
                v-if="loading"
            >
                <span class="message">{{ loadingMessage }}</span>
            </div>

            <div
                class="statistics-error has-text-danger"
                role="alert"
                v-if="errorMessage"
            >
                <span>{{ errorMessage }}</span>
                <button
                    class="button button-outlined-white is-small"
                    type="button"
                    @click="updateChartData"
                >
                    <app-icon icon="carbon:redo" />
                    <span class="ml-05">{{ msgRetry }}</span>
                </button>
            </div>

            <div
                class="chart-controls"
                v-if="canToggleObjectTypes"
            >
                <span aria-live="polite">
                    {{ showAllTypes ? msgShowingAllTypes : msgShowingTopTypes }}
                </span>
                <button
                    class="button button-outlined-white is-small"
                    type="button"
                    @click="showAllTypes = !showAllTypes"
                >
                    <app-icon icon="carbon:filter-reset" v-if="showAllTypes" />
                    <app-icon icon="carbon:filter" v-if="!showAllTypes" />
                    <span class="ml-05">{{ showAllTypes ? msgShowTopTypes : msgShowAllTypes }}</span>
                </button>
            </div>

            <details
                class="object-type-filter"
                v-if="typeChoice === '-' && objectTypeOptions.length"
            >
                <summary>
                    {{ msgFilterChartObjectTypes }}
                    <span
                        class="selected-count"
                        aria-live="polite"
                    >
                        ({{ msgSelectedTypes }}: {{ selectedObjectTypes.length }} / {{ objectTypeOptions.length }})
                    </span>
                </summary>
                <div class="object-type-filter-content">
                    <div class="object-type-filter-toolbar">
                        <div class="object-type-search">
                            <label for="statistics-object-type-search">{{ msgSearchObjectTypes }}</label>
                            <input
                                id="statistics-object-type-search"
                                type="search"
                                v-model="objectTypeSearch"
                            >
                        </div>
                        <div class="object-type-filter-actions">
                            <button
                                class="button button-outlined-white is-small"
                                type="button"
                                :disabled="selectedObjectTypes.length === objectTypeOptions.length"
                                @click="selectAllObjectTypes"
                            >
                                <app-icon icon="carbon:checkbox-checked" />
                                <span class="ml-05">{{ msgSelectAll }}</span>
                            </button>
                            <button
                                class="button button-outlined-white is-small"
                                type="button"
                                :disabled="selectedObjectTypes.length === 0"
                                @click="clearObjectTypes"
                            >
                                <app-icon icon="carbon:checkbox" />
                                <span class="ml-05">{{ msgClearAll }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="object-type-options">
                        <label
                            class="object-type-option"
                            v-for="item in filteredObjectTypeOptions"
                            :key="item.value"
                        >
                            <input
                                type="checkbox"
                                :value="item.value"
                                v-model="selectedObjectTypes"
                            >
                            <span
                                class="object-type-swatch"
                                :style="{ backgroundColor: item.color || '#ccc' }"
                                aria-hidden="true"
                            />
                            <span>{{ item.label }}</span>
                        </label>
                        <p
                            class="object-type-filter-empty"
                            v-if="filteredObjectTypeOptions.length === 0"
                        >
                            {{ msgNoMatchingObjectTypes }}
                        </p>
                    </div>
                </div>
            </details>

            <BarChart
                :chart-data="displayedStats"
                v-if="loaded && displayedStats.datasets.length > 0"
            />
            <p
                class="chart-empty"
                role="status"
                v-else-if="loaded"
            >
                {{ msgNoChartData }}
            </p>

            <section
                class="mt-15 totals"
                :aria-label="msgStatisticsTotals"
                v-if="loaded"
            >
                <div class="total-summary">
                    <span>{{ msgTotal }}</span>
                    <strong>{{ totalSum }}</strong>
                </div>
                <ul class="total-list">
                    <li
                        v-for="item in sortedTotals"
                        :key="item.label"
                        :style="{ borderLeftColor: item.color || '#ccc' }"
                    >
                        <span>{{ item.label }}</span>
                        <strong>{{ item.total }}</strong>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'AdminStatistics',

    components: {
        BarChart:() => import(/* webpackChunkName: "bar-chart" */'app/components/charts/bar-chart'),
    },

    props: {
        labels: {
            type: Object,
            required: true,
        },
        objectTypes: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            abstractTypes: ['media', 'objects'],
            basedayChoices: [
                { label: t`All days`, value: '-' },
                { label: t`Sunday`, value: 'sunday' },
                { label: t`Monday`, value: 'monday' },
                { label: t`Tuesday`, value: 'tuesday' },
                { label: t`Wednesday`, value: 'wednesday' },
                { label: t`Thursday`, value: 'thursday' },
                { label: t`Friday`, value: 'friday' },
                { label: t`Saturday`, value: 'saturday' },
            ],
            dayChoice: '-',
            dayChoices: [],
            daysOfWeek: ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            errorMessage: '',
            internalLabels:{
                month: [ t`Week` + ' 1', t`Week` + ' 2', t`Week` + ' 3', t`Week` + ' 4', t`Week` + ' 5' ],
                year: [ t`January`, t`February`, t`March`, t`April`, t`May`, t`June`, t`July`, t`August`, t`September`, t`October`, t`November`, t`December` ],
            },
            interval: 'year',
            loaded: false,
            loading: false,
            loadingMessage: '',
            monthChoice: '-',
            monthChoices: [],
            msgShowAllTypes: t`Show all object types`,
            msgShowTopTypes: t`Show top 8 object types`,
            msgFilterChartObjectTypes: t`Filter chart object types`,
            msgSearchObjectTypes: t`Search object types`,
            msgSelectedTypes: t`Selected`,
            msgSelectAll: t`Select all`,
            msgClearAll: t`Clear all`,
            msgNoMatchingObjectTypes: t`No object types match your search.`,
            msgNoChartData: t`No chart data for this selection.`,
            msgFetchError: t`Unable to load statistics. Please try again.`,
            msgRetry: t`Retry`,
            msgShowingAllTypes: t`Showing all object types`,
            msgShowingTopTypes: t`Showing top 8 object types by count`,
            msgTitle: t`Contents creation`,
            msgTotal: t`All contents`,
            msgStatisticsTotals: t`Statistics totals`,
            msgObjectType: t`Object type`,
            msgYear: t`Year`,
            msgMonth: t`Month`,
            msgWeek: t`Week`,
            msgDay: t`Day`,
            objectTypesChoices: [],
            objectTypeSearch: '',
            requestController: new AbortController(),
            selectedObjectTypes: [],
            stats: null,
            showAllTypes: false,
            totalColors: {},
            totals: {},
            totalSum: 0,
            topTypesLimit: 8,
            typeChoice: '-',
            weekChoice: '-',
            weekChoices: [],
            yearChoice: '-',
            yearChoices: [],
        }
    },

    computed: {
        objectTypeOptions() {
            return Object.keys(this.objectTypesChoices)
                .filter((key) => key !== '-')
                .map((key) => {
                    const item = this.objectTypesChoices[key];

                    return {
                        value: key,
                        label: this.labels[key] || item.label || item.name || key,
                        color: item.color,
                    };
                })
                .sort((a, b) => a.label.localeCompare(b.label));
        },

        filteredObjectTypeOptions() {
            const search = this.objectTypeSearch.trim().toLowerCase();

            return this.objectTypeOptions.filter((item) => item.label.toLowerCase().includes(search));
        },

        sortedTotals() {
            return Object.entries(this.totals)
                .map(([label, total]) => ({
                    label,
                    total,
                    color: this.totalColors[label],
                }))
                .sort((a, b) => b.total - a.total || a.label.localeCompare(b.label));
        },

        canToggleObjectTypes() {
            return this.typeChoice === '-'
                && this.stats !== null
                && this.stats.datasets.filter((dataset) => this.selectedObjectTypes.includes(dataset.objectType)).length > this.topTypesLimit;
        },

        displayedStats() {
            if (!this.stats) {
                return this.stats;
            }

            const selectedDatasets = this.typeChoice === '-'
                ? this.stats.datasets.filter((dataset) => this.selectedObjectTypes.includes(dataset.objectType))
                : this.stats.datasets;
            if (this.typeChoice !== '-' || this.showAllTypes) {
                return { ...this.stats, datasets: selectedDatasets };
            }

            const datasets = selectedDatasets
                .filter((dataset) => this.totals[dataset.label] > 0)
                .sort((a, b) => this.totals[b.label] - this.totals[a.label])
                .slice(0, this.topTypesLimit);

            return { ...this.stats, datasets };
        },
    },

    async mounted() {
        this.$nextTick(async () => {
            const firstChoice = { '-': { label: t`All object types`, name: '-' } };
            // sort object types by label or name
            for (const abstractType of this.abstractTypes) {
                delete this.objectTypes[abstractType];
            }
            this.objectTypesChoices = Object.keys(this.objectTypes).sort((a, b) => {
                const labelA = this.objectTypes[a].label || this.objectTypes[a].name;
                const labelB = this.objectTypes[b].label || this.objectTypes[b].name;
                return labelA.localeCompare(labelB);
            }).reduce((obj, key) => {
                obj[key] = this.objectTypes[key];
                return obj;
            }, {});
            this.objectTypesChoices = {...firstChoice, ... this.objectTypesChoices};
            this.selectedObjectTypes = Object.keys(this.objectTypesChoices).filter((key) => key !== '-');
            this.yearChoice = new Date().getFullYear();
            this.yearChoices = Array.from({ length: 10 }, (_, i) => new Date().getFullYear() - i);
            this.monthChoice = new Date().toLocaleString('en', { month: 'long' }).toLowerCase();
            this.weekChoice = Math.round(new Date().getDate() / 7) + 1;
            this.resetMonthChoices();
            this.resetWeekChoices();
            await this.updateChartData();
        });
    },

    methods: {

        changeDay() {
            this.updateChartData();
        },

        changeMonth() {
            this.dayChoice = '-';
            this.weekChoice = '-';
            this.resetWeekChoices();
            this.resetMonthsPerYear();
            this.updateChartData();
        },

        changeType() {
            this.updateChartData();
        },

        changeWeek() {
            this.dayChoice = '-';
            this.updateChartData();
        },

        changeYear() {
            this.dayChoice = '-';
            this.weekChoice = '-';
            this.monthChoice = '-';
            this.resetMonthChoices();
            this.resetMonthsPerYear();
            this.updateChartData();
        },

        selectAllObjectTypes() {
            this.selectedObjectTypes = this.objectTypeOptions.map((item) => item.value);
        },

        clearObjectTypes() {
            this.selectedObjectTypes = [];
        },


        async dataset(objectType, label, backgroundColor, filters, signal) {
            this.loadingMessage = t`Loading stats data for` + ' ' + label;
            const options = {
                method: 'GET',
                credentials: 'same-origin',
                signal,
                headers: {
                    'accept': 'application/json',
                    'X-CSRF-Token': BEDITA.csrfToken,
                },
            };
            let filter = `objectType=${objectType}&year=${filters.year}`;
            if (filters.month !== '-') {
                filter+= `&month=${filters.month}`;
            }
            if (filters.week !== '-') {
                filter+= `&week=${filters.week}`;
            }
            if (filters.day !== '-') {
                filter+= `&day=${filters.day}`;
            }
            const response = await fetch(`${BEDITA.base}/admin/statistics?${filter}`, options);
            if (!response.ok) {
                throw new Error(`Statistics request failed with status ${response.status}`);
            }
            const json = await response.json();
            if (!Array.isArray(json?.data)) {
                throw new Error('Statistics response did not contain data');
            }

            return {
                data: json.data,
                backgroundColor,
                label,
                objectType,
            };
        },

        async datasets(filters, signal) {
            const datasets = [];
            const keys = this.typeChoice !== '-' ? [this.typeChoice] : Object.keys(this.objectTypes);
            const i18nKeys = [];
            const colors = {};
            const types = {};
            this.totals = {};
            for (let i = 0; i < keys.length; i++) {
                if (this.labels[keys[i]] !== undefined) {
                    i18nKeys.push(this.labels[keys[i]]);
                    colors[this.labels[keys[i]]] = this.objectTypes[keys[i]]?.color || null;
                    types[this.labels[keys[i]]] = keys[i];
                }
            }
            const sortedKeys = i18nKeys.sort((a, b) => {
                return a.localeCompare(b);
            });
            this.totalColors = colors;
            this.totalSum = 0;
            for (let i = 0; i < sortedKeys.length; i++) {
                const objectType = types[sortedKeys[i]];
                const label = sortedKeys[i];
                const backgroundColor = colors[sortedKeys[i]];
                const data = await this.dataset(objectType, label, backgroundColor, filters, signal);
                let total = 0;
                for (let i = 0; i < data.data.length; i++) {
                    total = eval(total + data.data[i]);
                }
                if (total > 0) {
                    this.totals[label] = total;
                    this.totalSum = eval(this.totalSum + total);
                }
                datasets.push(data);
            }

            return datasets;
        },

        async fetchChartData(filters, signal) {
            const labels = [...this.internalLabels[this.interval]];
            const datasets = await this.datasets(filters, signal);

            return { labels, datasets };
        },

        resetMonthChoices() {
            this.monthChoices = [
                { label: t`All months`, value: '-' },
                { label: t`January`, value: 'january' },
                { label: t`February`, value: 'february' },
                { label: t`March`, value: 'march' },
                { label: t`April`, value: 'april' },
                { label: t`May`, value: 'may' },
                { label: t`June`, value: 'june' },
                { label: t`July`, value: 'july' },
                { label: t`August`, value: 'august' },
                { label: t`September`, value: 'september' },
                { label: t`October`, value: 'october' },
                { label: t`November`, value: 'november' },
                { label: t`December`, value: 'december' },
            ];
            const today = new Date();
            if (this.yearChoice === today.getFullYear()) {
                const month = today.toLocaleString('en', { month: 'long' }).toLowerCase();
                const months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
                for (let i = months.length; i > 0; i--) {
                    if (months[i-1] === month) {
                        break;
                    }
                    this.monthChoices.splice(i, 1);
                }
            }
        },

        resetMonthsPerYear() {
            const choices = this.monthChoices.filter((item) => item.value !== '-');
            this.internalLabels.year = choices.map((item) => item.label);
        },

        resetWeekChoices() {
            if (this.monthChoice === '-') {
                this.weekChoices = [
                    { label: t`All weeks`, value: '-' },
                ];
                this.internalLabels.month = [ t`Week` + ' 1', t`Week` + ' 2', t`Week` + ' 3', t`Week` + ' 4', t`Week` + ' 5' ];
                return;
            }
            if (this.monthChoice === 'february') {
                this.weekChoices = [
                    { label: t`All weeks`, value: '-' },
                    { label: this.weekLabel('1'), value: '1' },
                    { label: this.weekLabel('2'), value: '2' },
                    { label: this.weekLabel('3'), value: '3' },
                    { label: this.weekLabel('4'), value: '4' },
                ];
            } else {
                this.weekChoices = [
                    { label: t`All weeks`, value: '-' },
                    { label: this.weekLabel('1'), value: '1' },
                    { label: this.weekLabel('2'), value: '2' },
                    { label: this.weekLabel('3'), value: '3' },
                    { label: this.weekLabel('4'), value: '4' },
                    { label: this.weekLabel('5'), value: '5' },
                ];
            }
            const choices = this.weekChoices.filter((item) => item.value !== '-');
            this.internalLabels.month = choices.map((item) => item.label);
        },

        async updateChartData() {
            this.requestController.abort();
            this.requestController = new AbortController();
            const requestController = this.requestController;

            this.interval = 'year';
            if (this.dayChoice !== '-') {
                this.interval = 'day';
                this.internalLabels.day = [this.dayChoices.find((item) => item.value === this.dayChoice).label];
            } else if (this.weekChoice !== '-') {
                this.dayChoice = '-';
                this.interval = 'week';
                const firstDayNumber = this.weekChoice * 7 - 6;
                const month = this.monthChoices.findIndex((item) => item.value === this.monthChoice)-1;
                let counter = firstDayNumber;
                this.internalLabels.week = [];
                let newDayChoices = [];
                newDayChoices.push({ label: t`All days`, value: '-' });
                for (let i = 0; i < 7; i++) {
                    const day = counter + i;
                    const d = new Date(this.yearChoice, month, day);
                    const m = d.toLocaleString('en', { month: 'long' }).toLowerCase();
                    if (m !== this.monthChoice) {
                        break;
                    }
                    const ds = this.basedayChoices.find((item) => item.value === this.daysOfWeek[d.getDay()]).label;
                    this.internalLabels.week.push(`${ds} ${d.getDate()}`);
                    newDayChoices.push({ label: `${ds} ${d.getDate()}`, value: `${d.getDate()} ${m} ${this.yearChoice}` });
                }
                this.dayChoices = newDayChoices;
            } else if (this.monthChoice !== '-') {
                this.dayChoice = '-';
                this.weekChoice = '-';
                this.interval = 'month';
            }
            this.loaded = false;
            this.loading = true;
            this.errorMessage = '';
            this.stats = null;
            const filters = {
                year: this.yearChoice,
                month: this.monthChoice,
                week: this.weekChoice,
                day: this.dayChoice,
            };
            try {
                const stats = await this.fetchChartData(filters, requestController.signal);
                if (requestController.signal.aborted) {
                    return;
                }
                this.stats = stats;
                this.loaded = true;
            } catch (e) {
                if (!requestController.signal.aborted) {
                    console.error(e);
                    this.errorMessage = this.msgFetchError;
                }
            } finally {
                if (!requestController.signal.aborted) {
                    this.loading = false;
                    this.$forceUpdate();
                }
            }
        },

        weekLabel(week) {
            const day = week * 7 - 6;
            const month = this.monthChoices.findIndex((item) => item.value === this.monthChoice)-1;
            const ds = new Date(this.yearChoice, month, day);
            let next, de;
            for (let i = 0; i < 7; i++) {
                next = new Date(this.yearChoice, month, day + i);
                if (next.toLocaleString('en', { month: 'long' }).toLowerCase() !== this.monthChoice) {
                    break;
                }
                de = next;
            }

            return t`Week` + ` ${week} [${ds.getDate()} - ${de.getDate()}]`;
        },
    }
}
</script>
<style>
div.admin-statistics {
    border: dotted 1px #ccc;
    padding: 1rem;
    max-width: 1200px;
    background-color: #fff;
    border-radius: 25px;
}
div.admin-statistics select {
    background-color: #fff;
}
div.admin-statistics .filter-field {
    display: inline-flex;
    flex-direction: column;
    margin: 0 1rem 0.75rem 0;
}
div.admin-statistics .filter-field label {
    color: #000;
}
div.admin-statistics .chart-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 1rem;
    margin-bottom: 0.5rem;
    color: #000;
}
div.admin-statistics .object-type-filter {
    min-width: 0;
    margin: 0.75rem 0;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
}
div.admin-statistics .object-type-filter summary {
    padding: 0.75rem;
    color: #000;
    font-weight: bold;
    cursor: pointer;
}
div.admin-statistics .object-type-filter[open] > summary {
    border-bottom: 1px solid #ccc;
}
div.admin-statistics .object-type-filter .selected-count {
    font-weight: normal;
}
div.admin-statistics .object-type-filter-content {
    padding: 0.75rem;
}
div.admin-statistics .object-type-filter-toolbar,
div.admin-statistics .object-type-filter-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 0.5rem 1rem;
}
div.admin-statistics .object-type-search {
    display: flex;
    flex: 1 1 14rem;
    flex-direction: column;
    gap: 0.25rem;
}
div.admin-statistics .object-type-search label {
    margin-right: 0;
    color: #000;
}
div.admin-statistics .object-type-search input {
    box-sizing: border-box;
    width: 100%;
    min-width: 0;
}
div.admin-statistics .object-type-filter-actions {
    align-items: center;
}
div.admin-statistics .object-type-filter-actions .selected-count {
    color: #000;
}
div.admin-statistics .object-type-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr));
    gap: 0.25rem 0.75rem;
    max-height: 12rem;
    margin-top: 0.75rem;
    overflow-y: auto;
}
div.admin-statistics .object-type-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
    margin-right: 0;
    padding: 0.25rem;
    color: #000;
    cursor: pointer;
}
div.admin-statistics .object-type-option input {
    flex: 0 0 auto;
    margin: 0;
}
div.admin-statistics .object-type-swatch {
    flex: 0 0 0.75rem;
    width: 0.75rem;
    height: 0.75rem;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 50%;
}
div.admin-statistics .object-type-option span:last-child {
    min-width: 0;
    overflow-wrap: anywhere;
}
div.admin-statistics .object-type-filter-empty,
div.admin-statistics .chart-empty {
    margin: 0.5rem 0;
    color: #000;
}
div.admin-statistics .chart-empty {
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
}
div.admin-statistics .statistics-error {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
    margin: 0.75rem 0;
}
div.admin-statistics h2, div.admin-statistics .message, div.admin-statistics .totals {
    color: #000;
}
div.admin-statistics .totals {
    display: grid;
    gap: 0.75rem;
}
div.admin-statistics .total-summary,
div.admin-statistics .total-list li {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    border: 1px solid #ccc;
    border-left: 4px solid #ccc;
    border-radius: 0.25rem;
}
div.admin-statistics .total-summary {
    background-color: #f5f5f5;
    font-weight: bold;
}
div.admin-statistics .total-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr));
    gap: 0.5rem;
    margin: 0;
    padding: 0;
    list-style: none;
}
div.admin-statistics .total-list li span {
    overflow-wrap: anywhere;
}
div.admin-statistics .total-list li strong {
    white-space: nowrap;
}
</style>
