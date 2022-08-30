import moment from 'moment';
import { t } from 'ttag';

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/calendar.twig
 *
 * <date-ranges-view> component used for ModulesPage -> View
 */
export default {
    template: `
        <div>
            <div class="date-ranges-list">
                <div class="date-ranges-item mb-1" v-for="(dateRange, index) in dateRanges" style="grid-template-columns: 200px 200px 120px 120px 120px">
                    <div>
                        <span>${t`From`}</span>
                        <div :class="dateRangeClass(dateRange)">
                            <input type="text"
                                :name="getName(index, 'start_date')"
                                v-model="dateRange.start_date"
                                @change="onDateChanged(dateRange, $event)"
                                v-datepicker="true" date="true" :time="!dateRange.params.all_day" daterange="true"
                            />
                        </div>
                    </div>
                    <div :class="dateRangeClass(dateRange)">
                        <span>${t`To`}</span>
                        <div>
                            <input type="text"
                                :name="getName(index, 'end_date')"
                                v-model="dateRange.end_date"
                                @change="onDateChanged(dateRange, $event)"
                                v-datepicker="true" date="true" :time="!dateRange.params.all_day" daterange="true"
                            />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="m-0 nowrap has-text-size-smaller">
                            <input type="checkbox"
                                :name="getNameAllDay(index)"
                                v-model="dateRange.params.all_day"
                                @change="onAllDayChanged(dateRange, $event)" />
                            ${t`All day`}
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="m-0 nowrap has-text-size-smaller" v-if="isDaysInterval(dateRange)">
                            <input type="checkbox"
                                :name="getNameEveryDay(index)"
                                v-model="dateRange.params.every_day"
                                @change="onEveryDayChanged(dateRange, $event)"
                                checked="dateRange.params.every_day" />
                            ${t`Every day`}
                        </label>
                    </div>
                    <div class="mb-2">
                        <button @click.prevent="remove(index, $event)" :disabled="dateRanges.length < 2">${t`Remove`}</button>
                    </div>
                    <div v-if="dateRange.params.every_day === false" class="m-0 nowrap has-text-size-smaller weekdays">
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.sunday" />${t`Sunday`}</label>
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.monday" />${t`Monday`}</label>
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.tuesday" />${t`Tuesday`}</label>
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.wednesday" />${t`Wednesday`}</label>
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.thursday" />${t`Thursday`}</label>
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.friday" />${t`Friday`}</label>
                        <label><input type="checkbox" v-model="dateRange.params.weekdays.saturday" />${t`Saturday`}</label>
                        <input type="hidden" :name="getName(index, 'params')" :value="JSON.stringify(dateRange.params)" />
                    </div>
                    <div v-if="msdiff(dateRange) < 0" class="icon-error">${t`Invalid date range`}</div>
                </div>
            </div>

            <button @click.prevent="add">${t`Add`}</button>
        </div>
    `,

    props: {
        ranges: String,
    },

    data() {
        return {
            dateRanges: [],
        }
    },

    created() {
        const ranges = JSON.parse(this.ranges);
        if (ranges) {
            this.dateRanges = ranges;
            this.dateRanges.forEach((range) => {
                if (range.params?.length > 0) {
                    range.params = JSON.parse(range.params) || false;
                }
                range.params = range.params || { all_day: false, every_day: true, weekdays: {} };
                range.params.every_day = !range.params?.weekdays || Object.keys(range.params?.weekdays).length === 0;
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
                    all_day: false,
                },
            });
        },
        /**
         * Handle date change.
         *
         * @param {Object} dateRange The date range object
         * @param {Event} ev The event.
         * @returns {void}
         */
        onDateChanged(dateRange, ev) {
            const value = ev.target.value;
            const dr = {};
            if (ev.target.name.indexOf('start_date') > 0) {
                dr.start_date = value;
                dr.end_date = dateRange.end_date;
            } else {
                dr.start_date = dateRange.start_date;
                dr.end_date = value;
            }
            this.validate(dr);
            if (!value || !dateRange.params.all_day) {
                return;
            }
            this.setAllDayRange(dateRange);
        },
        /**
         * Handle changes to `all_day` value of a date range.
         * When checked, the range must start at 00:00 and end at 23:59 of the `start_date`
         * (or `end_date` if it's the only date).
         *
         * @param {Object} dateRange Date range object.
         * @param {boolean} checked The value of `all_day` flag
         * @returns {void}
         */
        onAllDayChanged(dateRange, { target: { checked } }) {
            if (!checked) {
                return;
            }
            this.setAllDayRange(dateRange);
        },
        onEveryDayChanged(dateRange, { target: { checked } }) {
            if (!checked) {
                this.resetWeekdays(dateRange);
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
        },
        /**
         * Adjust range dates to be a single full day event.
         * The range must start at 00:00 and end at 23:59 of the `start_date`
         * (or `end_date` if it's the only date).
         *
         * @param {Object} dateRange Date range object.
         * @returns {void}
         */
        setAllDayRange(dateRange) {
            if (!dateRange.start_date && !dateRange.end_date) {
                return;
            }
            let date = moment(dateRange.start_date || dateRange.end_date);
            date.set({ hour: 0, minute: 0, second: 0, millisecond: 0 });
            dateRange.start_date = date.format();
            dateRange.end_date = date.endOf('day').format();
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
    },
}
