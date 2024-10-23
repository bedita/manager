<template>
    <div>
        <select v-model="filterType" @change="updateChartData">
            <option v-for="item,key in objectTypesChoices" :key="key" :value="key">{{ item.label || item.name }}</option>
        </select>

        <select v-model="timeInterval" @change="updateChartData">
            <option v-for="item in timeIntervals" :key="item.value" :value="item.value">{{ item.label }}</option>
        </select>

        <select v-model="yearNumber" @change="updateChartData" v-if="timeInterval === 'year'">
            <option v-for="item in yearNumbers" :key="item" :value="item">{{ item }}</option>
        </select>

        <select v-model="timeIntervalChoice" @change="updateChartData">
            <option v-for="item in timeIntervalChoices" :value="item.value">{{ item.label }}</option>
        </select>

        <div class="is-loading-spinner mt-05" v-if="loading">{{ loadingMessage }}</div>

        <BarChart
            :chart-data="stats"
            v-if="loaded"
        />
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
            labels: {
                week: [ t`Monday`, t`Tuesday`, t`Wednesday`, t`Thursday`, t`Friday`, t`Saturday`, t`Sunday` ],
                month: [ t`Week 1`, t`Week 2`, t`Week 3`, t`Week 4` ],
                year: [ t`January`, t`February`, t`March`, t`April`, t`May`, t`June`, t`July`, t`August`, t`September`, t`October`, t`November`, t`December` ],
            },
            loaded: false,
            loading: false,
            loadingMessage: '',
            stats: null,
            filterType: '-',
            objectTypesChoices: [],
            timeInterval: 'week',
            timeIntervalChoice: '-',
            timeIntervalChoices: [],
            timeIntervals: [
                {
                    label: t`Week`,
                    value: 'week',
                    choices: [
                        { label: t`All days`, value: '-' },
                        { label: t`Monday`, value: 'monday' },
                        { label: t`Tuesday`, value: 'tuesday' },
                        { label: t`Wednesday`, value: 'wednesday' },
                        { label: t`Thursday`, value: 'thursday' },
                        { label: t`Friday`, value: 'friday' },
                        { label: t`Saturday`, value: 'saturday' },
                        { label: t`Sunday`, value: 'sunday' },
                    ],
                },
                {
                    label: t`Month`,
                    value: 'month',
                    choices: [
                        { label: t`All weeks`, value: '-' },
                        { label: t`Week 1`, value: 'week1' },
                        { label: t`Week 2`, value: 'week2' },
                        { label: t`Week 3`, value: 'week3' },
                        { label: t`Week 4`, value: 'week4' },
                    ],
                },
                {
                    label: t`Year`,
                    value: 'year',
                    choices: [
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
                },
            ],
            yearNumber: '-',
            yearNumbers: [],
        }
    },

    async mounted() {
        this.$nextTick(async () => {
            const firstChoice = { '-': { label: t`All object types`, name: '-' } }
            this.objectTypesChoices = {...firstChoice, ... this.objectTypes};
            this.yearNumber = new Date().getFullYear();
            this.yearNumbers = Array.from({ length: 10 }, (_, i) => new Date().getFullYear() - i);
            await this.updateChartData();
        });
    },

    methods: {

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
                let filter = `objectType=${objectType}&interval=${this.timeInterval}&sub=${this.timeIntervalChoice}`;
                if (this.timeInterval === 'year') {
                    filter += `&year=${this.yearNumber}`;
                }
                const response = await fetch(`${BEDITA.base}/admin/stats?${filter}`, options);
                const json = await response.json();
                console.log(objectType, this.timeInterval, this.timeIntervalChoice, this.yearNumber, json.data);
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
            const keys = this.filterType !== '-' ? [this.filterType] : Object.keys(this.objectTypes);
            console.log(keys);
            for (let i = 0; i < keys.length; i++) {
                const objectType = keys[i];
                console.log(objectType);
                const backgroundColor = this.objectTypes[objectType]?.color || null;
                const data = await this.dataset(objectType, backgroundColor);
                datasets.push(data);
            }

            return datasets;
        },

        async fetchChartData() {
            const datasets = await this.datasets();
            let labels = this.labels[this.timeInterval];
            if (this.timeIntervalChoice !== '-') {
                labels = [this.timeIntervalChoices.find(item => item.value === this.timeIntervalChoice)?.label || '-'];
            }

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
            console.log('updateChartData', this.filterType, this.timeInterval, this.timeIntervalChoice);
            this.timeIntervalChoices = this.timeIntervals.find(item => item.value === this.timeInterval).choices;
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
