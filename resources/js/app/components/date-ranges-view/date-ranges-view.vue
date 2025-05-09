<template>
    <div>
        <div class="date-ranges-list">
            <DateRange
                v-for="(dateRange, index) in dateRanges"
                :key="index"
                :compact="compact"
                :options="options"
                :readonly="readonly"
                :source="dateRanges[index]"
                @remove="remove"
                @undoRemove="undoRemove"
                @update="update"
            />
        </div>
        <button
            class="button button-primary"
            @click.prevent="add"
            v-if="!readonly"
        >
            <app-icon icon="carbon:add" />
            <span class="ml-05">{{ msgAdd }}</span>
        </button>
        <input
            type="hidden"
            name="date_ranges"
            v-model="dateRangesJson"
        >
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'DateRangesView',

    components: {
        DateRange: () => import(/* webpackChunkName: "date-range" */ '../date-range/date-range.vue'),
    },

    props: {
        compact: {
            type: Boolean,
            default: false,
        },
        ranges: {
            type: String,
            default: undefined,
        },
        options: {
            type: Object,
            default: () => ({}),
        },
        readonly: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            dateRanges: [],
            dateRangesJson: '',
            msgAdd: t`Add`,
        }
    },

    created() {
        const ranges = JSON.parse(this.ranges);
        const defaultOptions = {
            all_day: this.options?.all_day || false,
            every_day: this.options?.every_day || true,
            weekdays: {},
        };
        if (ranges) {
            this.dateRanges = ranges;
            this.dateRanges.forEach((range) => {
                range.uuid = this.uuid();
                if (range.params?.length > 0) {
                    range.params = JSON.parse(range.params) || false;
                }
                range.originalParams = range.params;
                range.params = range.params || defaultOptions;
                if (!this.optionIsSet('every_day')) {
                    range.params.weekdays = this.formatWeekdays(range?.originalParams?.weekdays);
                    const daysCount = Object.values(range.params.weekdays).filter((v) => v === true).length;
                    range.params.every_day = daysCount === 0;
                }
                if (!range.start_date) {
                    range.end_date = '';
                }
            });
            this.dateRangesJson = JSON.stringify(this.dateRanges);
        }
        if (!this.dateRanges.length) {
            this.add();
        }
    },

    methods: {
        add() {
            const newRange = {
                start_date: '',
                end_date: '',
                params: {
                    all_day: this.options?.all_day || false,
                    every_day: this.options?.every_day || true,
                    weekdays: {},
                },
            };
            newRange.uuid = this.uuid();
            this.dateRanges.push(newRange);
            this.dateRangesJson = JSON.stringify(this.dateRanges);
        },
        formatWeekdays(wdays) {
            if (!wdays) {
                return {
                    sunday: false,
                    monday: false,
                    tuesday: false,
                    wednesday: false,
                    thursday: false,
                    friday: false,
                    saturday: false
                };
            }
            return {
                sunday: wdays?.sunday || false,
                monday: wdays?.monday || false,
                tuesday: wdays?.tuesday || false,
                wednesday: wdays?.wednesday || false,
                thursday: wdays?.thursday || false,
                friday: wdays?.friday || false,
                saturday: wdays?.saturday || false
            };
        },
        optionIsSet(option) {
            return this.options[option] !== undefined;
        },
        remove(range) {
            this.update(range);
        },
        undoRemove(range) {
            this.dateRangesJson = JSON.stringify(this.dateRanges);
            this.update(range);
        },
        update(range) {
            let json = JSON.parse(this.dateRangesJson);
            const dr = [];
            for (let data of json) {
                if (data.removed === true) {
                    if (data.uuid === range.uuid && range.removed === false) {
                        dr.push(this.updateData(range));
                    }
                    continue;
                }
                if (data.uuid !== range.uuid) {
                    dr.push(this.updateData(data));
                    continue;
                }
                if (!range?.removed) {
                    dr.push(this.updateData(range));
                }
            }
            this.dateRangesJson = JSON.stringify(dr);
            this.$emit('update', this.dateRangesJson);
        },
        updateData(data) {
            return {
                uuid: data.uuid,
                removed: data.removed,
                start_date: data.start_date,
                end_date: data.end_date,
                params: data.params,
            }
        },
        uuid() {
            return Math.random().toString(36).substring(2) + Date.now().toString(36);
        },
    },
}
</script>
