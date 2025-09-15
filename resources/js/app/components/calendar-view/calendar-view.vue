<template>
    <div class="calendar-view">
        <template v-if="createNew">
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
                        <h2>{{ msgCreateNew }}</h2>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div class="container">
                        <form-field
                            v-for="field in fieldsRequired"
                            :key="field"
                            :field="fieldKey(field)"
                            :render-as="fieldType(field)"
                            :json-schema="schema?.properties?.[fieldKey(field)] || {}"
                            :is-uploadable="false"
                            :languages="languages"
                            :object-type="objectType"
                            :required="fieldsRequired?.includes(fieldKey(field))"
                            :val="schema?.[fieldKey(field)] || null"
                            v-model="formFieldProperties[objectType][fieldKey(field)]"
                            @error="fieldError"
                            @update="fieldUpdate"
                            @success="fieldSuccess"
                        />
                        <template v-for="field in fieldsOther">
                            <div
                                :key="field"
                                v-if="fieldKey(field) === 'date_ranges'"
                            >
                                <date-ranges-view
                                    :compact="true"
                                    :ranges="createNewDateRanges"
                                    @update="updateNewDateRanges"
                                />
                            </div>
                            <form-field
                                :key="field"
                                :field="fieldKey(field)"
                                :render-as="fieldType(field)"
                                :json-schema="schema?.properties?.[fieldKey(field)] || {}"
                                :is-uploadable="false"
                                :languages="languages"
                                :object-type="objectType"
                                :required="fieldsRequired?.includes(fieldKey(field))"
                                :val="schema?.[fieldKey(field)] || null"
                                v-model="formFieldProperties[objectType][fieldKey(field)]"
                                @error="fieldError"
                                @update="fieldUpdate"
                                @success="fieldSuccess"
                                v-else
                            />
                        </template>
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                :class="{'is-loading-spinner': saving}"
                                :disabled="saveDisabled"
                                @click.prevent="save"
                            >
                                <app-icon icon="carbon:save" />
                                <span class="ml-05">
                                    {{ msgSave }}
                                </span>
                            </button>
                            <button
                                class="button button-primary"
                                @click="closePanel()"
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
        <div
            class="loading"
            v-if="loading"
        >
            <span class="is-loading-spinner" />
            <span class="ml-05">{{ msgLoading }} ...</span>
        </div>
        <div
            id="loading-background"
            v-if="loading"
        />
        <FullCalendar
            id="full-calendar"
            :options="calendarOptions"
            ref="fullCal"
        >
            <template #eventContent="arg">
                <div class="eventContainer">
                    <div>
                        <span>{{ ftime(arg.event.start) }}<template v-if="arg.event.end"> - {{ ftime(arg.event.end) }}</template></span>
                        <object-info
                            border-color="transparent"
                            color="white"
                            :object-data="arg?.event?.extendedProps?.obj"
                            v-if="arg?.event?.extendedProps?.obj"
                        />
                    </div>
                    <div>
                        <span
                            class="event-title"
                            v-title="arg?.event?.extendedProps?.obj?.attributes?.title || arg.event.title"
                        >
                            {{ arg.event.title }}
                        </span>
                    </div>
                </div>
            </template>
        </FullCalendar>
    </div>
</template>
<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import enLocale from '@fullcalendar/core/locales/en-gb';
import itLocale from '@fullcalendar/core/locales/it';
import { t } from 'ttag';

export default {
    name: 'CalendarView',
    components: {
        DateRangesView: () => import(/* webpackChunkName: "date-ranges-view" */'app/components/date-ranges-view/date-ranges-view'),
        FormField: () => import(/* webpackChunkName: "form-field" */'app/components/fast-create/form-field'),
        FullCalendar,
        ObjectInfo: () => import(/* webpackChunkName: "object-info" */'app/components/object-info/object-info'),
    },
    inject: ['getCSFRToken'],
    props: {
        languages: {
            type: Object,
            default: () => {},
        },
        objectType: {
            type: String,
            required: true,
        },
        schema: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            calendarOptions: {
                locale: (BEDITA?.locale?.slice(0,2) || 'it') === 'it' ? itLocale : enLocale,
                plugins: [
                    interactionPlugin,
                    listPlugin,
                    dayGridPlugin,
                    timeGridPlugin,
                ],
                headerToolbar: {
                    left: 'prev,next,today,addButton',
                    center: 'datePicker',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                customButtons: {
                    addButton: {
                        text: `+ ${t`Create`}`,
                        click: () => {
                            this.prepareNew('', new Date().toISOString().split('T')[0]);
                        }
                    },
                    datePicker: {
                        text: '',
                        click: () => {}
                    }
                },
                contentHeight: 'auto',
                eventDisplay: 'block',
                eventColor: '#378006',
                eventTextColor: '#fff',
                eventBackgroundColor: BEDITA?.currentModule?.color || '#378006',
                eventBorderColor: '#378006',
                displayEventTime: true,
                displayEventEnd: true,
                slotMinTime: '08:00:00',
                allDaySlot: false,
                initialView: 'dayGridMonth',
                fixedWeekCount: false,
                nowIndicator: true,
                editable: false,
                initialEvents: [],
                events: async (fetchInfo) => {
                    this.startDate = fetchInfo?.startStr?.split('T')?.[0];
                    this.endDate = fetchInfo?.endStr?.split('T')?.[0];
                    const items = await this.search();

                    return [...items];
                },
                dateClick: async (info) => {
                    this.prepareNew('', info.dateStr);
                },
                datesSet: (info) => {
                    this.currentView = info.view.type;
                    this.$nextTick(() => this.renderCustomPicker());
                },
            },
            createNew: false,
            createNewDateRanges: [],
            currentView: 'dayGridMonth',
            error: {},
            fieldsMap: {},
            fieldsAll: [],
            fieldsInvalid: [],
            fieldsOther: [],
            fieldsRequired: [],
            formFieldProperties: {},
            loaded: false,
            loading: false,
            msgClose: t`Close`,
            msgCreateNew: t`Create new`,
            msgLoading: t`Loading`,
            msgSave: t`Save`,
            msgTitle: t`Title`,
            pageSize: 100,
            saving: false,
            success: {},
        }
    },
    computed: {
        saveDisabled() {
            return this.fieldsInvalid.length > 0 || this.saving;
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.formFieldProperties[this.objectType] = {};
            this.fieldsRequired = BEDITA?.fastCreateFields?.[this.objectType]?.required || [];
            this.fieldsAll = BEDITA?.fastCreateFields?.[this.objectType]?.fields || ['id', 'title', 'date_ranges'];
            this.fieldsAll = this.fieldsAll.map(field => {
                if (typeof field === 'object') {
                    return Object.keys(field)[0];
                }
                return field;
            });
            this.fieldsOther = this.fieldsAll.filter(field => !this.fieldsRequired.includes(field));
            const fields = BEDITA?.fastCreateFields?.[this.objectType]?.fields || [];
            let ff = fields;
            if (fields.constructor === Object) {
                ff = Object.keys(fields);
                this.fieldsMap = fields;
            }
            for (const item of ff) {
                if (item.constructor === Object) {
                    const itemKey = Object.keys(item)[0];
                    this.fieldsMap[itemKey] = item[itemKey];
                }
            }
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectType][f]);

            setTimeout(() => {
                this.renderCustomPicker();
            }, 0);
        });
    },
    methods: {
        closePanel() {
            this.createNew = false;
            this.createNewDateRanges = [];
        },
        fieldError(field, val) {
            this.error[field] = val;
        },
        fieldUpdate(field, val) {
            this.formFieldProperties[this.objectType][field] = val;
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectType][f]);
        },
        fieldSuccess(field, val) {
            this.success[field] = val;
        },
        fieldKey(field) {
            return this.isNumeric(field) ? this.fieldsMap[field] : field;
        },
        fieldType(field) {
            return !this.isNumeric(field) ? this.fieldsMap[field] : null;
        },
        formatDate(date) {
            const d = new Date(date);

            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        },
        formatMonth(date) {
            const d = new Date(date);

            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
        },
        formatTime(date) {
            const d = new Date(date);

            return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
        },
        ftime(d) {
            return this.$helpers.formatTime(d);
        },
        isNumeric(str) {
            if (typeof str != 'string') {
                return false;
            }

            return !isNaN(str) && !isNaN(parseFloat(str));
        },
        prepareNew(title, startDate) {
            this.createNew = true;
            this.createNewDateRanges = JSON.stringify([{start_date: startDate}]);
            this.formFieldProperties[this.objectType].date_ranges = [{start_date: startDate}];
        },
        refetchEvents() {
            const calendarApi = this.$refs.fullCal.getApi();
            calendarApi.refetchEvents();
        },
        renderCustomPicker() {
            const toolbar = document.querySelector('.fc-toolbar-chunk .fc-datePicker-button');
            if (!toolbar) return;
            toolbar.innerHTML = ''; // Clear previous picker

            let input;
            if (this.currentView === 'dayGridMonth' || this.currentView === 'listMonth') {
                input = document.createElement('input');
                input.type = 'month';
                input.value = this.formatMonth(this.$refs.fullCal.getApi().getDate());
                input.onchange = (e) => {
                    const [year, month] = e.target.value.split('-');
                    this.$refs.fullCal.getApi().gotoDate(new Date(year, month - 1, 1));
                };
                toolbar.appendChild(input);

                return;
            }
            if (this.currentView === 'timeGridWeek' || this.currentView === 'listWeek') {
                input = document.createElement('input');
                input.type = 'date';
                input.value = this.formatDate(this.$refs.fullCal.getApi().getDate());
                input.onchange = (e) => {
                    this.$refs.fullCal.getApi().gotoDate(new Date(e.target.value));
                };
                toolbar.appendChild(input);

                return;
            }
            if (this.currentView === 'timeGridDay' || this.currentView === 'listDay') {
                // Show the default title (e.g., "15 settembre 2025")
                const date = this.$refs.fullCal.getApi().getDate();
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                const locale = (BEDITA?.locale?.slice(0,2) || 'it') === 'it' ? 'it-IT' : 'en-GB';
                const title = date.toLocaleDateString(locale, options);
                const span = document.createElement('span');
                span.textContent = title;
                span.style.fontWeight = 'bold';
                toolbar.appendChild(span);

                return;
            }
            // fallback: month picker
            input = document.createElement('input');
            input.type = 'month';
            input.value = this.formatMonth(this.$refs.fullCal.getApi().getDate());
            input.onchange = (e) => {
                const [year, month] = e.target.value.split('-');
                this.$refs.fullCal.getApi().gotoDate(new Date(year, month - 1, 1));
            };
            toolbar.appendChild(input);
        },
        async save() {
            try {
                this.saving = true;
                this.payload = Object.assign({}, this.formFieldProperties[this.objectType]);
                this.payload.date_ranges = JSON.parse(this.createNewDateRanges);
                const url = `/${this.objectType}/save`;
                const options = {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': this.getCSFRToken(),
                    },
                    body: JSON.stringify(this.payload),
                };
                const response = await fetch(url, options);
                const responseJson = await response.json();
                if (responseJson?.error) {
                    throw new Error(responseJson.error);
                }
                this.createNew = false;
                this.createNewDateRanges = [];
                this.refetchEvents();
            } catch (error) {
                this.error = error;
            } finally {
                this.saving = false;
            }
        },
        async search() {
            const items = [];
            try {
                this.loading = true;
                const headers = new Headers({
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': BEDITA.csrfToken,
                });
                const options = {
                    credentials: 'same-origin',
                    headers,
                    method: 'GET',
                };
                let query = `?fields=${this.fieldsAll.join(',')}&page=1`;
                query += `&filter[date_ranges][from_date]=${this.startDate}&filter[date_ranges][to_date]=${this.endDate}`;
                query += `&lang=${BEDITA?.locale?.slice(0,2) || 'it'}&sort=date_ranges_min_start_date&page_size=${this.pageSize}`;
                let response = await fetch(`${BEDITA.base}/api/${this.objectType}${query}`, options);
                let responseJson = await response.json();
                let pageCount = responseJson?.meta?.pagination?.page_count || 1;
                let count = 1;
                this.searchItemsConcat(items, responseJson);
                while (count < pageCount) {
                    count++;
                    query = `?fields=${this.fieldsAll.join(',')}&page=${count}`;
                    query += `&filter[date_ranges][from_date]=${this.startDate}&filter[date_ranges][to_date]=${this.endDate}`;
                    query += `&lang=${this.calendarOptions.locale}&sort=date_ranges_min_start_date&page_size=${this.pageSize}`;
                    response = await fetch(`${BEDITA.base}/api/${this.objectType}${query}`, options);
                    responseJson = await response.json();
                    this.searchItemsConcat(items, responseJson);
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            } finally {
                this.loading = false;
            }

            return items;
        },
        searchItemsConcat(items, responseJson) {
            if (!responseJson?.data) {
                return;
            }
            for (const item of responseJson.data) {
                for (const subItem of item.attributes.date_ranges) {
                    if (subItem.start_date) {
                        items.push({
                            url: `/view/${item.id}`,
                            title: this.$helpers.truncate(item?.attributes?.title || item?.attributes?.uname, 50),
                            start: new Date(subItem?.start_date),
                            end: subItem?.end_date ? new Date(subItem?.end_date) : null,
                            obj: item,
                        });
                    }
                }
                item.attributes.date_ranges.map(dateRange => ({
                    ...dateRange,
                    start_date: new Date(dateRange.start_date),
                    end_date: new Date(dateRange.end_date),
                }));
            }
        },
        updateNewDateRanges(ranges) {
            this.createNew.date_ranges = ranges;
        },
    },
}
</script>
<style scoped>
.calendar-view .loading {
    display: flex;
    position: fixed;
    z-index: 999;
    overflow: show;
    margin: auto;
    top: 0;
    left: 180px;
    bottom: 0;
    right: 0;
    width: 100px;
    height: 100px;
}
.calendar-view #loading-background {
    position: fixed;
    top:0;
    left:0;
    bottom:0;
    right:0;
	width:100%;
    background-color: rgba(255,255,255,0.1);
    z-index:9999;
}
.calendar-view aside.main-panel-container {
    z-index: 9999;
}
.calendar-view aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
.calendar-view button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
.calendar-view .container {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.calendar-view .container > div {
    display: flex;
    flex-direction: column;
}
.calendar-view .container > div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
}
.calendar-view .eventContainer {
    display: flex;
    flex-direction: column;
    border: solid 1px gray;
}
.calendar-view .eventContainer > div {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 0.1rem;
    margin: 0.1rem;
}
.calendar-view .eventContainer > div > span.event-title {
    text-wrap: wrap;
    font-weight: 700;
}
</style>
