<template>
    <div class="stats">
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

            <div class="is-loading-spinner mt-05" v-if="loading"><span class="loadingMessage">{{ loadingMessage }}</span></div>

            <BarChart
                :chart-data="stats"
                v-if="loaded"
            />
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'AdminStats',

    components: {
        BarChart:() => import(/* webpackChunkName: "bar-chart" */'app/components/charts/bar-chart'),
    },

    props: {
        objectTypes: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
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
            interval: 'year',
            labels: {
                month: [ t`Week 1`, t`Week 2`, t`Week 3`, t`Week 4`, t`Week 5` ],
                year: [ t`January`, t`February`, t`March`, t`April`, t`May`, t`June`, t`July`, t`August`, t`September`, t`October`, t`November`, t`December` ],
            },
            loaded: false,
            loading: false,
            loadingMessage: '',
            monthChoice: '-',
            monthChoices: [
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
            ],
            msgTitle: t`Contents creation`,
            objectTypesChoices: [],
            stats: null,
            typeChoice: '-',
            weekChoice: '-',
            weekChoices: [
                { label: t`All weeks`, value: '-' },
                { label: t`Week 1`, value: '1' },
                { label: t`Week 2`, value: '2' },
                { label: t`Week 3`, value: '3' },
                { label: t`Week 4`, value: '4' },
                { label: t`Week 5`, value: '5' },
            ],
            yearChoice: '-',
            yearChoices: [],
        }
    },

    async mounted() {
        this.$nextTick(async () => {
            const firstChoice = { '-': { label: t`All object types`, name: '-' } }
            this.objectTypesChoices = {...firstChoice, ... this.objectTypes};
            // sort object types by label or name
            this.objectTypesChoices = Object.keys(this.objectTypesChoices).sort((a, b) => {
                const labelA = this.objectTypesChoices[a].label || this.objectTypesChoices[a].name;
                const labelB = this.objectTypesChoices[b].label || this.objectTypesChoices[b].name;
                return labelA.localeCompare(labelB);
            }).reduce((obj, key) => {
                obj[key] = this.objectTypesChoices[key];
                return obj;
            }, {});
            this.yearChoice = new Date().getFullYear();
            this.yearChoices = Array.from({ length: 10 }, (_, i) => new Date().getFullYear() - i);
            this.monthChoice = new Date().toLocaleString('en', { month: 'long' }).toLowerCase();
            this.weekChoice = Math.round(new Date().getDate() / 7) + 1;
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
            if (this.monthChoice === 'february') {
                this.weekChoices = [
                    { label: t`All weeks`, value: '-' },
                    { label: t`Week 1`, value: '1' },
                    { label: t`Week 2`, value: '2' },
                    { label: t`Week 3`, value: '3' },
                    { label: t`Week 4`, value: '4' },
                ];
            } else {
                this.weekChoices = [
                    { label: t`All weeks`, value: '-' },
                    { label: t`Week 1`, value: '1' },
                    { label: t`Week 2`, value: '2' },
                    { label: t`Week 3`, value: '3' },
                    { label: t`Week 4`, value: '4' },
                    { label: t`Week 5`, value: '5' },
                ];
            }
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
            this.updateChartData();
        },


        async dataset(objectType, backgroundColor) {
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
                const response = await fetch(`${BEDITA.base}/admin/stats?${filter}`, options);
                const json = await response.json();
                data = json.data;
            } catch (error) {
                console.error(error);
            }

            return {
                data,
                backgroundColor,
                label: objectType,
            };
        },

        async datasets() {
            const datasets = [];
            const keys = this.typeChoice !== '-' ? [this.typeChoice] : Object.keys(this.objectTypes);
            for (let i = 0; i < keys.length; i++) {
                const objectType = keys[i];
                const backgroundColor = this.objectTypes[objectType]?.color || null;
                const data = await this.dataset(objectType, backgroundColor);
                datasets.push(data);
            }

            return datasets;
        },

        async fetchChartData() {
            const datasets = await this.datasets();
            const labels = this.labels[this.interval];

            return { labels, datasets };
        },

        rarray(size) {
            return Array.from({ length: size }, () => this.rnum());
        },

        rnum() {
            const min = 0;
            const max = 10000;

            return Math.floor(Math.random() * (max - min + 1) + min);
        },

        async updateChartData() {
            this.interval = 'year';
            if (this.dayChoice !== '-') {
                this.interval = 'day';
                this.labels.day = [this.dayChoices.find((item) => item.value === this.dayChoice).label];
            } else if (this.weekChoice !== '-') {
                this.dayChoice = '-';
                this.interval = 'week';
                const firstDayNumber = this.weekChoice * 7 - 6;
                const month = this.monthChoices.findIndex((item) => item.value === this.monthChoice)-1;
                let counter = firstDayNumber;
                this.labels.week = [];
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
                    this.labels.week.push(`${ds} ${d.getDate()}`);
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
    }
}
</script>
<style>
div.stats {
    border: dotted 1px #ccc;
    padding: 1rem;
    max-width: 1200px;
    background-color: #fff;
    border-radius: 25px;
}
div.stats select {
    margin-right: 1rem;
    background-color: #fff;
}
div.stats h2, div.stats span.loadingMessage {
    color: #000;
}
</style>
