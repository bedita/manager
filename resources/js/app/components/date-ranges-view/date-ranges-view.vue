<template>
    <div>
        <div class="date-ranges-list">
            <div class="date-ranges-item mb-1"
                 v-for="(dateRange, index) in dateRanges"
                 :key="index"
            >
                <div>
                    <span>{{ msgFrom }}</span>
                    <div :class="dateRangeClass(dateRange)">
                        <input type="text"
                               :name="getName(index, 'start_date')"
                               date="true"
                               :time="!dateRange.params.all_day"
                               daterange="true"
                               v-model="dateRange.start_date"
                               v-datepicker="true"
                               @change="onDateChanged(dateRange, $event)"
                        >
                    </div>
                </div>
                <div :class="dateRangeClass(dateRange)">
                    <span>{{ msgTo }}</span>
                    <div>
                        <input type="text"
                               :name="getName(index, 'end_date')"
                               date="true"
                               :time="!dateRange.params.all_day"
                               daterange="true"
                               v-model="dateRange.end_date"
                               v-datepicker="true"
                               @change="onDateChanged(dateRange, $event)"
                               v-if="dateRange.start_date"
                        >
                        <input type="text"
                               disabled="disabled"
                               v-else
                        >
                    </div>
                </div>
                <div v-show="!optionIsSet('all_day')">
                    <label class="m-0 nowrap has-text-size-smaller">
                        <input type="checkbox"
                               :disabled="!dateRange.start_date"
                               :name="getNameAllDay(index)"
                               v-model="dateRange.params.all_day"
                               @change="onAllDayChanged(dateRange, $event)"
                        >
                        {{ msgAllDay }}
                    </label>
                </div>
                <div v-show="!optionIsSet('every_day')">
                    <label class="m-0 nowrap has-text-size-smaller">
                        <input type="checkbox"
                               :disabled="!isDaysInterval(dateRange)"
                               :name="getNameEveryDay(index)"
                               checked="dateRange.params.every_day"
                               v-model="dateRange.params.every_day"
                               @change="onEveryDayChanged(dateRange, $event)"
                        >
                        {{ msgEveryDay }}
                    </label>
                </div>
                <div>
                    <button :disabled="dateRanges.length < 2"
                            class="button button-primary"
                            @click.prevent="remove(index, $event)"
                    >
                        <app-icon icon="carbon:trash-can" />
                        <span class="ml-05">{{ msgRemove }}</span>
                    </button>
                </div>
                <div class="m-0 nowrap has-text-size-smaller weekdays"
                     v-if="dateRange.params.every_day === false"
                >
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'sunday')"
                            v-model="dateRange.params.weekdays.sunday"
                        >{{ msgSunday }}
                    </label>
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'monday')"
                            v-model="dateRange.params.weekdays.monday"
                        >{{ msgMonday }}
                    </label>
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'tuesday')"
                            v-model="dateRange.params.weekdays.tuesday"
                        >{{ msgTuesday }}
                    </label>
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'wednesday')"
                            v-model="dateRange.params.weekdays.wednesday"
                        >{{ msgWednesday }}
                    </label>
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'thursday')"
                            v-model="dateRange.params.weekdays.thursday"
                        >{{ msgThursday }}
                    </label>
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'friday')"
                            v-model="dateRange.params.weekdays.friday"
                        >{{ msgFriday }}
                    </label>
                    <label>
                        <input
                            type="checkbox"
                            :name="getNameWeekday(index, 'saturday')"
                            v-model="dateRange.params.weekdays.saturday"
                        >{{ msgSaturday }}
                    </label>
                    <input
                        type="hidden"
                        :name="getName(index, 'params')"
                        :value="jsonParams(index)"
                    >
                </div>
                <div class="icon-error"
                     v-if="msdiff(dateRange) < 0"
                >
                    {{ msgInvalidDateRange }}
                </div>
            </div>
        </div>

        <button class="button button-primary"
                @click.prevent="add"
        >
            <app-icon icon="carbon:add" />
            <span class="ml-05">{{ msgAdd }}</span>
        </button>
    </div>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';

export default {

    props: {
        ranges: {
            type: String,
            default: undefined,
        },
        options: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            dateRanges: [],
            msgAdd: t`Add`,
            msgAllDay: t`All day`,
            msgEveryDay: t`Every day`,
            msgFriday: t`Friday`,
            msgFrom: t`From`,
            msgInvalidDateRange: t`Invalid date range`,
            msgMonday: t`Monday`,
            msgRemove: t`Remove`,
            msgSaturday: t`Saturday`,
            msgSunday: t`Sunday`,
            msgThursday: t`Thursday`,
            msgTo: t`To`,
            msgTuesday: t`Tuesday`,
            msgWednesday: t`Wednesday`,
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
                if (range.params?.length > 0) {
                    range.params = JSON.parse(range.params) || false;
                }
                range.params = range.params || defaultOptions;
                if (!this.optionIsSet('every_day')) {
                    range.params.every_day = !range.params?.weekdays || Object.keys(range.params?.weekdays).length === 0;
                    range.params.weekdays = range?.params?.weekdays ? this.normalizeWeekdays(range?.params?.weekdays) : {};
                }
                if (!range.start_date) {
                    range.end_date = '';
                }
            });
        }
        if (!this.dateRanges.length) {
            this.add();
        }
    },

    methods: {
        getName(index, field) {
            return `date_ranges[${index}][${field}]`;
        },
        getNameAllDay(index) {
            return `date_ranges[${index}][params][all_day]`;
        },
        getNameEveryDay(index) {
            return `date_ranges[${index}][params][every_day]`;
        },
        getNameWeekday(index, weekday) {
            return `date_ranges[${index}][params][weekdays][${weekday}]`;
        },
        jsonParams(index) {
            return JSON.stringify(this.dateRanges[index]?.params);
        },
        isDaysInterval(range) {
            if (!range.start_date) {
                return false;
            }
            if (!range.end_date) {
                return false;
            }
            const sd = moment(range.start_date);
            const ed = moment(range.end_date);
            const diff = moment.duration(ed.diff(sd)).asDays();

            return diff >= 1;
        },
        normalizeWeekdays(wdays) {
            if (wdays.constructor !== Array) {
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
                sunday: wdays.includes('sunday'),
                monday: wdays.includes('monday'),
                tuesday: wdays.includes('tuesday'),
                wednesday: wdays.includes('wednesday'),
                thursday: wdays.includes('thursday'),
                friday: wdays.includes('friday'),
                saturday: wdays.includes('saturday'),
            };
        },
        /**
         * Add an empty date range to list.
         *
         * @returns {void}
         */
        add() {
            this.dateRanges.push({
                start_date: '',
                end_date: '',
                params: {
                    all_day: this.options?.all_day || false,
                },
            });
        },
        onDateChanged(dateRange, ev) {
            const value = ev.target.value;
            const dr = {};
            const isStartDate = ev.target.name.indexOf('start_date') > 0;
            if (isStartDate) {
                dr.start_date = value;
                if (value.length === 0) {
                    dateRange.end_date = '';
                }
                dr.end_date = dateRange.end_date;
            } else {
                dr.start_date = dateRange.start_date;
                dr.end_date = value;
            }
            this.validate(dr);
            if (this.isDaysInterval(dateRange) && !this.optionIsSet('all_day')) {
                dateRange.params.all_day = false;
            }
            if (this.options?.all_day === true) {
                if (isStartDate) {
                    dateRange.start_date = value;
                } else {
                    dateRange.end_date = value;
                }
                this.setAllDayRange(dateRange);
            }
        },
        onAllDayChanged(dateRange, { target: { checked } }) {
            if (!checked) {
                return;
            }
            this.setAllDayRange(dateRange);
        },
        onEveryDayChanged(dateRange, { target: { checked } }) {
            if (!checked) {
                this.resetWeekdays(dateRange);
            } else {
                this.setAllWeekdays(dateRange);
            }
        },
        resetWeekdays(dateRange) {
            dateRange.params.weekdays = {
                sunday: false,
                monday: false,
                tuesday: false,
                wednesday: false,
                thursday: false,
                friday: false,
                saturday: false
            };
            this.$forceUpdate();
        },
        setAllWeekdays(dateRange) {
            dateRange.params.weekdays = {
                sunday: true,
                monday: true,
                tuesday: true,
                wednesday: true,
                thursday: true,
                friday: true,
                saturday: true
            };
            this.$forceUpdate();
        },
        setAllDayRange(dateRange) {
            if (!dateRange.start_date) {
                return;
            }
            let date = moment(dateRange.start_date);
            date.set({ hour: 0, minute: 0 });
            dateRange.start_date = date.format('YYYY-MM-DDTHH:mm');
            if (!dateRange.end_date) {
                dateRange.end_date = date.endOf('day').format('YYYY-MM-DDTHH:mm');
            } else {
                date = moment(dateRange.end_date);
                date.set({ hour: 23, minute: 59 });
                dateRange.end_date = date.format('YYYY-MM-DDTHH:mm');
            }
        },
        /**
         * Remove date range from list.
         *
         * @param {Integer} index The index
         * @returns {void}
         */
        remove(index) {
            this.dateRanges.splice(index, 1);
        },
        /**
         * Validate date range
         *
         * @param {Object} dateRange Date range object.
         * @returns {void}
         */
        validate(dateRange) {
            const button = document.querySelector('button[form=form-main]');
            const valid = this.valid(dateRange);
            button.disabled = valid ? false : 'disabled';
        },
        /**
         * Check date range is valid
         *
         * @param {Object} dateRange Date range object.
         * @returns {Boolean}
         */
        valid(dateRange) {
            if (dateRange.start_date === '' || dateRange.end_date === '') {
                return true;
            }

            return this.msdiff(dateRange) > 0;
        },
        /**
         * Milliseconds diff between end_date and start_date in a dateRange.
         * When negative, it means that start_date is after end_date.
         *
         * @param {Object} dateRange
         * @returns {Integer}
         */
        msdiff(dateRange) {
            if (dateRange.start_date === '' || dateRange.end_date === '') {
                return 0;
            }
            const sd = moment(dateRange.start_date);
            const ed = moment(dateRange.end_date);

            return moment.duration(ed.diff(sd)).asMilliseconds();
        },
        /**
         * Return class for dateRange text field
         *
         * @param {Object} dateRange Date range object.
         * @returns
         */
        dateRangeClass(dateRange) {
            if (this.msdiff(dateRange) < 0) {
                return 'invalid';
            }

            return '';
        },

        optionIsSet(option) {
            return this.options[option] !== undefined;
        },
    },
}
</script>
