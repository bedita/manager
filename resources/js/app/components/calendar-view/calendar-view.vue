<template>
    <div class="calendar-view">
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
        />
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
        FullCalendar,
    },
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
            },
            loading: false,
            msgLoading: t`Loading`,
            pageSize: 100,
        }
    },
    methods: {
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
                let query = '?fields=id,title,date_ranges';
                query += `&filter[date_ranges][from_date]=${this.startDate}&filter[date_ranges][to_date]=${this.endDate}`;
                query += `&lang=${BEDITA?.locale?.slice(0,2) || 'it'}&sort=date_ranges_min_start_date&page_size=${this.pageSize}`;
                let response = await fetch(`${BEDITA.base}/api/${this.objectType}${query}`, options);
                let responseJson = await response.json();
                let pageCount = responseJson?.meta?.pagination?.page_count || 1;
                let count = 1;
                this.searchItemsConcat(items, responseJson);
                while (count < pageCount) {
                    count++;
                    query = `?fields=id,title,date_ranges&page=${count}`;
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
                            title: item?.attributes?.title || item?.attributes?.uname,
                            start: new Date(subItem?.start_date),
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
</style>
