<template>
    <div class="admin-statistics">
        <h2>{{ msgTitle }}</h2>
        <div>
            <!-- object type -->
            <select v-model="typeChoice" @change="changeType">
                <option v-for="item,key in objectTypesChoices" :key="key" :value="key">{{ item.label || item.name }}</option>
            </select>

            <!-- year -->
            <select v-model="yearChoice" @change="changeYear">
                <option v-for="item in yearChoices" :key="item" :value="item">{{ item }}</option>
            </select>

            <!-- month -->
            <select v-model="monthChoice" @change="changeMonth">
                <option v-for="item in monthChoices" :value="item.value">{{ item.label }}</option>
            </select>

            <!-- week -->
            <select v-model="weekChoice" @change="changeWeek" :disabled="monthChoice == '-'">
                <option v-for="item in weekChoices" :value="item.value">{{ item.label }}</option>
            </select>

            <!-- day -->
            <select v-model="dayChoice" @change="changeDay" :disabled="weekChoice == '-'">
                <option v-for="item in dayChoices" :value="item.value">{{ item.label }}</option>
            </select>

            <div class="is-loading-spinner mt-05" v-if="loading"><span class="message">{{ loadingMessage }}</span></div>

            <BarChart
                :chart-data="stats"
                v-if="loaded"
            />

            <div class="mt-15 totals" v-if="loaded">
                <b>{{ msgTotal }}</b>: <b>{{ totalSum }}</b> | <span class="message" v-for="key in Object.keys(totals)" :key="key">{{ key }}: {{ totals[key] }} | </span>
            </div>
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
            msgTitle: t`Contents creation`,
            msgTotal: t`All contents`,
            objectTypesChoices: [],
            stats: null,
            totals: {},
            totalSum: 0,
            typeChoice: '-',
            weekChoice: '-',
            weekChoices: [],
            yearChoice: '-',
            yearChoices: [],
        }
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


        async dataset(objectType, label, backgroundColor) {
            let data = [];
            try {
                this.loadingMessage = t`Loading stats data for` + ' ' + objectType;
                const options = {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                };
                let filter = `objectType=${objectType}&year=${this.yearChoice}`;
                if (this.monthChoice !== '-') {
                    filter+= `&month=${this.monthChoice}`;
                }
                if (this.weekChoice !== '-') {
                    filter+= `&week=${this.weekChoice}`;
                }
                if (this.dayChoice !== '-') {
                    filter+= `&day=${this.dayChoice}`;
                }
                const response = await fetch(`${BEDITA.base}/admin/statistics?${filter}`, options);
                const json = await response.json();
                data = json.data;
            } catch (error) {
                console.error(error);
            }

            return {
                data,
                backgroundColor,
                label,
            };
        },

        async datasets() {
            const datasets = [];
            const keys = this.typeChoice !== '-' ? [this.typeChoice] : Object.keys(this.objectTypes);
            const i18nKeys = [];
            const colors = {};
            const types = {};
            this.totals = {};
            for (let i = 0; i < keys.length; i++) {
                i18nKeys.push(this.labels[keys[i]]);
                colors[this.labels[keys[i]]] = this.objectTypes[keys[i]]?.color || null;
                types[this.labels[keys[i]]] = keys[i];
            }
            const sortedKeys = i18nKeys.sort((a, b) => {
                return a.localeCompare(b);
            });
            this.totalSum = 0;
            for (let i = 0; i < sortedKeys.length; i++) {
                const objectType = types[sortedKeys[i]];
                const label = sortedKeys[i];
                const backgroundColor = colors[sortedKeys[i]];
                const data = await this.dataset(objectType, label, backgroundColor);
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

        async fetchChartData() {
            const datasets = await this.datasets();
            const labels = this.internalLabels[this.interval];

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
            this.stats = null;
            try {
                const stats = await this.fetchChartData();
                this.stats = stats;
                this.loaded = true;
            } catch (e) {
                console.error(e)
            } finally {
                this.loading = false;
                this.$forceUpdate();
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
    margin-right: 1rem;
    background-color: #fff;
}
div.admin-statistics h2, div.admin-statistics .message, div.admin-statistics div.totals {
    color: #000;
}
</style>
