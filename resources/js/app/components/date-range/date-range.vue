<template>
    <div
        class="dateRange date-ranges-item mb-1"
        :class="removed ? 'removed' : 'active'"
    >
        <div>
            <span>{{ msgFrom }}</span>
            <div :class="dateRangeClass()">
                <input
                    type="text"
                    date="true"
                    :time="!all_day"
                    daterange="true"
                    v-model="start_date"
                    v-datepicker="true"
                    @change="onDateChanged(true, $event)"
                    v-if="ready"
                >
            </div>
        </div>
        <div :class="dateRangeClass()">
            <span>{{ msgTo }}</span>
            <div>
                <template v-if="start_date">
                    <input
                        type="text"
                        date="true"
                        :time="!all_day"
                        daterange="true"
                        v-model="end_date"
                        v-datepicker="true"
                        @change="onDateChanged(false, $event)"
                        v-if="ready"
                    >
                </template>
                <input
                    type="text"
                    disabled="disabled"
                    v-else
                >
            </div>
        </div>
        <div v-show="!optionIsSet('all_day')">
            <label class="m-0 nowrap has-text-size-smaller">
                <input
                    type="checkbox"
                    :disabled="!start_date"
                    :checked="all_day"
                    @change="onAllDayChanged($event)"
                >
                {{ msgAllDay }}
            </label>
        </div>
        <div v-show="!optionIsSet('every_day')">
            <label class="m-0 nowrap has-text-size-smaller">
                <input
                    type="checkbox"
                    :disabled="!isDaysInterval()"
                    :checked="every_day"
                    @change="onEveryDayChanged($event)"
                >
                {{ msgEveryDay }}
            </label>
        </div>
        <div v-if="!readonly">
            <button
                class="button button-primary"
                @click.prevent="remove($event)"
                v-if="!removed"
            >
                <app-icon icon="carbon:trash-can" />
                <span class="ml-05">{{ msgRemove }}</span>
            </button>
            <button
                class="button button-primary"
                @click.prevent="undoRemove($event)"
                v-else
            >
                <app-icon icon="carbon:undo" />
                <span class="ml-05">{{ msgUndo }}</span>
            </button>
        </div>
        <div
            class="m-0 nowrap has-text-size-smaller weekdays"
            v-show="every_day === false"
        >
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.sunday"
                    @click="updateWeekDays('sunday', $event)"
                >{{ msgSunday }}
            </label>
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.monday"
                    @click="updateWeekDays('monday', $event)"
                >{{ msgMonday }}
            </label>
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.tuesday"
                    @click="updateWeekDays('tuesday', $event)"
                >{{ msgTuesday }}
            </label>
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.wednesday"
                    @click="updateWeekDays('wednesday', $event)"
                >{{ msgWednesday }}
            </label>
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.thursday"
                    @click="updateWeekDays('thursday', $event)"
                >{{ msgThursday }}
            </label>
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.friday"
                    @click="updateWeekDays('friday', $event)"
                >{{ msgFriday }}
            </label>
            <label>
                <input
                    type="checkbox"
                    v-model="weekdays.saturday"
                    @click="updateWeekDays('saturday', $event)"
                >{{ msgSaturday }}
            </label>
        </div>
        <div
            class="icon-error"
            v-show="!start_date"
        >
            {{ msgEmptyDateRange }}
        </div>
        <div
            class="icon-error"
            v-show="validate() === false"
        >
            {{ msgInvalidDateRange }}
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import { t } from 'ttag';

export default {
    name: 'DateRange',
    props: {
        options: {
            type: Object,
            default: () => ({}),
        },
        readonly: {
            type: Boolean,
            default: false,
        },
        source: {
            type: Object,
            required: true
        },
    },

    data() {
        return {
            ready: false,
            every_day: true,
            start_date: '',
            all_day: '',
            end_date: '',
            weekdays: [],
            range: null,
            removed: false,
            msgAllDay: t`All day`,
            msgEmptyDateRange: t`Insert date(s)`,
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
            msgUndo: t`Undo`,
            msgWednesday: t`Wednesday`,
        }
    },

    mounted() {
        this.$nextTick(() => {
            this.range = this.source;
            this.start_date = this.range?.start_date;
            this.end_date = this.range?.end_date;
            this.all_day = this.range?.params?.all_day;
            this.every_day = this.range?.params?.every_day;
            this.weekdays = this.range?.params?.weekdays || {
                sunday: false,
                monday: false,
                tuesday: false,
                wednesday: false,
                thursday: false,
                friday: false,
                saturday: false
            };
            this.ready = true;
        });
    },

    methods: {
        dateRangeClass() {
            return this.msdiff() < 0 ? 'invalid' : '';
        },
        hasChanged(skip) {
            if (skip) {
                return true;
            }

            return this.start_date !== this.range.start_date
                || this.end_date !== this.range.end_date
                || this.all_day !== this.range.params.all_day
                || this.every_day !== this.range.params.every_day
                || JSON.stringify(this.weekdays) !== JSON.stringify(this.range.params.weekdays);
        },
        isDaysInterval() {
            if (!this.start_date) {
                return false;
            }
            if (!this.end_date) {
                return false;
            }
            const sd = moment(this.start_date);
            const ed = moment(this.end_date);
            const diff = moment.duration(ed.diff(sd)).asDays();

            return diff >= 1;
        },
        msdiff(dateRange) {
            const input = dateRange || this.range;
            if (!input?.start_date || !input?.end_date) {
                return 0;
            }
            const sd = moment(input?.start_date);
            const ed = moment(input?.end_date);

            return moment.duration(ed.diff(sd)).asMilliseconds();
        },
        onAllDayChanged({ target: { checked } }) {
            this.all_day = checked;
            if (!checked) {
                this.update();
                return;
            }
            this.setAllDayRange();
            this.update();
        },
        onDateChanged(isStartDate, ev) {
            const value = ev.target.value;
            const dr = {};
            if (isStartDate) {
                dr.start_date = value;
                if (value.length === 0) {
                    this.end_date = '';
                }
                dr.end_date = this.end_date;
            } else {
                dr.start_date = this.start_date;
                dr.end_date = value;
            }
            if (!this.validate(dr)) {
                return;
            }
            if (isStartDate) {
                this.start_date = value;
            } else {
                this.end_date = value;
            }
            if (this.isDaysInterval() && !this.optionIsSet('all_day')) {
                this.all_day = false;
            }
            if (this.options?.all_day === true) {
                this.setAllDayRange();
            }
            this.update();
        },
        onEveryDayChanged({ target: { checked } }) {
            this.every_day = checked;
            if (!checked) {
                this.resetWeekdays();
            } else {
                this.setAllWeekdays();
            }
            this.update();
        },
        optionIsSet(option) {
            return this.options[option] !== undefined;
        },
        remove() {
            this.removed = true;
            this.range.removed = true;
            this.$emit('remove', this.range);
        },
        resetWeekdays() {
            this.weekdays = {
                sunday: false,
                monday: false,
                tuesday: false,
                wednesday: false,
                thursday: false,
                friday: false,
                saturday: false
            };
        },
        setAllDayRange() {
            if (!this.start_date) {
                return;
            }
            let date = moment(this.start_date);
            date.set({ hour: 0, minute: 0 });
            this.start_date = date.toISOString();
            if (!this.end_date) {
                this.end_date = date.endOf('day').toISOString();
            } else {
                date = moment(this.end_date);
                date.set({ hour: 23, minute: 59 });
                this.end_date = date.toISOString();
            }
        },
        setAllWeekdays() {
            this.weekdays = {
                sunday: true,
                monday: true,
                tuesday: true,
                wednesday: true,
                thursday: true,
                friday: true,
                saturday: true
            };
        },
        undoRemove() {
            this.removed = false;
            this.range.removed = false;
            this.$emit('undoRemove', this.range);
        },
        update(skip) {
            if (this.hasChanged(skip) === false) {
                return;
            }
            this.range.start_date = this.start_date;
            this.range.end_date = this.end_date;
            this.range.params.all_day = this.all_day;
            this.range.params.every_day = this.every_day;
            this.range.params.weekdays = this.weekdays;
            if (!this.validate(this.range, skip)) {
                return;
            }
            this.$emit('update', this.range);
        },
        updateWeekDays(day, { target: { checked } }) {
            this.range.params.weekdays[day] = checked;
            this.update(true);
        },
        validate(dateRange, skip) {
            const input = dateRange || this.range;
            const button = document.querySelector('button[form=form-main]');
            const valid = skip || input?.start_date ? true : this.msdiff(input) > 0;
            if (button) {
                button.disabled = input?.start_date && !valid ? 'disabled' : false;
            }

            return valid;
        },
    },
}
</script>
<style>
div.removed {
    opacity: 0.5;
}
</style>
