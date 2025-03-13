<template>
    <div
        class="index-date-ranges"
        :class="showAll? 'show-all' : ''"
    >
        <div>
            <div
                v-for="(item, index) in items"
                :key="index"
            >
                <div
                    class="date-range"
                    v-show="showAll || index < 1"
                >
                    <div class="date-item">
                        {{ isInterval(item) ? msgFrom : msgOn }}
                        <span
                            class="date"
                            v-title="fmtFull(item?.start_date)"
                        >
                            {{ dateStart(item) }}
                        </span>
                    </div>
                    <div
                        class="date-item"
                        v-if="isInterval(item)"
                    >
                        {{ msgTo }}
                        &nbsp;
                        <span
                            class="date"
                            v-title="fmtFull(item?.end_date)"
                        >
                            {{ dateEnd(item) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="items?.length > 1">
            <button
                class="show-toggle icon icon-only-icon"
                :title="msgMore"
                @click.stop.prevent="showAll = !showAll"
                v-if="showAll"
            >
                <app-icon icon="carbon:subtract" />
                <span class="is-sr-only">{{ msgMore }}</span>
            </button>
            <button
                class="show-toggle icon icon-only-icon"
                :title="msgMore"
                @click.stop.prevent="showAll = !showAll"
                v-else
            >
                <app-icon icon="carbon:add" />
                <span class="is-sr-only">{{ msgMore }}</span>
            </button>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'DateRangesList',
    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            locale: BEDITA?.locale?.slice(0, 2) || 'en',
            msgFrom: t`from`,
            msgMore: t`More`,
            msgOn: t`on`,
            msgTo: t`to`,
            showAll: false,
        };
    },
    methods: {
        dateEnd(dateRange) {
            return !dateRange?.end_date ? '' : `${this.fmtDate(dateRange?.end_date)} ${this.fmtTime(dateRange?.end_date)}`;
        },
        dateStart(dateRange) {
            if (!dateRange?.start_date) {
                return '';
            }
            if (dateRange?.params?.all_day || false) {
                return this.fmtDate(dateRange?.start_date);
            }
            return `${this.fmtDate(dateRange?.start_date)} ${this.fmtTime(dateRange?.start_date)}`;
        },
        fmtDate(d) {
            return d ?  new Date(d).toLocaleDateString(this.locale, {dateStyle: 'medium'}) : '';
        },
        fmtFull(d) {
            const dd = d ?  new Date(d).toLocaleDateString(this.locale, {dateStyle: 'full'}) : '';
            const tt = d ?  new Date(d).toLocaleTimeString(this.locale, {timeStyle: 'full'}) : '';

            return `${dd} ${tt}`;
        },
        fmtTime(d) {
            return d ?  new Date(d).toLocaleTimeString(this.locale, {timeStyle: 'short'}) : '';
        },
        isInterval(dateRange) {
            return dateRange?.end_date && !(dateRange?.params?.all_day || false);
        },
    },
}
</script>
