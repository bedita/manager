<template>
    <div class="calendar-view">
        <aside
            class="main-panel-container on"
            custom-footer="true"
            custom-header="true"
            v-if="createNew"
        >
            <div class="main-panel fieldset">
                <header class="mx-1 mt-1 tab tab-static unselectable">
                    <h2>Create new</h2>
                </header>
                <div class="container">
                    <div>
                        <label for="title">{{ msgTitle }}</label>
                        <input
                            id="title"
                            class="title"
                            type="text"
                            v-model="createNewTitle"
                        >
                    </div>
                    <div>
                        <date-ranges-view
                            :compact="true"
                            :ranges="createNewDateRanges"
                            @update="updateNewDateRanges"
                        />
                    </div>
                    <div class="buttons">
                        <button
                            class="button button-primary"
                            :class="{'is-loading-spinner': saving}"
                            :disabled="saving"
                            @click="save"
                        >
                            <app-icon icon="carbon:save" />
                            <span class="ml-05">
                                {{ msgSave }}
                            </span>
                        </button>
                        <button
                            class="button button-primary"
                            @click="cancel"
                        >
                            <app-icon icon="carbon:reset" />
                            <span class="ml-05">
                                {{ msgCancel }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>
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
        FullCalendar,
        ObjectInfo: () => import(/* webpackChunkName: "object-info" */'app/components/object-info/object-info'),
    },
    inject: ['getCSFRToken'],
    props: {
        objectType: {
            type: String,
            required: true,
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
                    left: 'prev,next,today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
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
                    this.createNew = {
                        title: '',
                        date_ranges: [{start_date: info.dateStr}],
                    };
                    this.createNewDateRanges = JSON.stringify([{start_date: info.dateStr}]);
                    this.createNewTitle = '';
                },
            },
            createNew: false,
            createNewDateRanges: [],
            createNewTitle: '',
            fields: [],
            loading: false,
            msgCancel: t`Cancel`,
            msgLoading: t`Loading`,
            msgSave: t`Save`,
            msgTitle: t`Title`,
            pageSize: 100,
            saving: false,
        }
    },
    mounted() {
        this.$nextTick(() => {
            const moduleFields = BEDITA?.indexLists?.[this.objectType] || [];
            const defaultFields = ['id', 'title', 'date_ranges'];
            const allFields = [...defaultFields, ...moduleFields];
            this.fields = allFields.filter((value, index, array) => {
                return typeof value === 'string' && array.indexOf(value) === index;
            });
        });
    },
    methods: {
        cancel() {
            this.createNew = false;
            this.createNewDateRanges = [];
            this.createNewTitle = '';
        },
        ftime(d) {
            return this.$helpers.formatTime(d);
        },
        refetchEvents() {
            const calendarApi = this.$refs.fullCal.getApi();
            calendarApi.refetchEvents();
        },
        async save() {
            try {
                this.saving = true;
                this.payload = Object.assign({}, this.createNew);
                this.payload.title = this.createNewTitle;
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
                this.createNewTitle = '';
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
                let query = `?fields=${this.fields.join(',')}&page=1`;
                query += `&filter[date_ranges][from_date]=${this.startDate}&filter[date_ranges][to_date]=${this.endDate}`;
                query += `&lang=${BEDITA?.locale?.slice(0,2) || 'it'}&sort=date_ranges_min_start_date&page_size=${this.pageSize}`;
                let response = await fetch(`${BEDITA.base}/api/${this.objectType}${query}`, options);
                let responseJson = await response.json();
                let pageCount = responseJson?.meta?.pagination?.page_count || 1;
                let count = 1;
                this.searchItemsConcat(items, responseJson);
                while (count < pageCount) {
                    count++;
                    query = `?fields=${this.fields.join(',')}&page=${count}`;
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
