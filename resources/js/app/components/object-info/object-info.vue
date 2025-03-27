<template>
    <div class="object-info mr-05">
        <a
            :title="msgShowObjectInfo"
            class="button button-outlined-white is-small"
            @click.prevent="showInfo()"
        >
            <app-icon icon="carbon:information" />
        </a>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ObjectInfo',
    props: {
        objectData: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            fields: ['title', 'description'],
            labelsMap: new Map(),
            msgShowObjectInfo: t`Show object info`,
            values: {},
        };
    },
    mounted() {
        this.$nextTick(() => {
            const source = BEDITA?.indexLists?.[this.objectData?.type] || {};
            this.fields = source || ['title', 'description'];
            for (const field of this.fields) {
                this.labelsMap.set(field, BEDITA_I18N?.[field] || field);
                this.values[field] = this.objectData?.relationships?.streams?.data?.[0]?.attributes?.[field]
                    || this.objectData?.relationships?.streams?.data?.[0]?.meta?.[field]
                    || this.objectData?.attributes?.[field]
                    || '-';
                if (field === 'file_size') {
                    this.values[field] = this.$helpers.formatBytes(this.values[field]);
                }
            }

        });
    },
    methods: {
        categories(data) {
            if (!data?.length) {
                return '-';
            }
            return data.map((category) => {
                return `<span>${category.labels.default}</span>`;
            }).join(', ');
        },
        dateRanges(data) {
            if (!data?.length) {
                return '-';
            }
            return data.map((dateRange) => {
                return dateRange.end_date ? `<div>${this.$helpers.formatDate(dateRange.start_date)} - ${this.$helpers.formatDate(dateRange.end_date)}</div>` : `<div>${this.$helpers.formatDate(dateRange.start_date)}</div>`;
            }).join(' ');
        },
        getFieldVal(val) {
            if (!val) {
                return '<span>-</span>';
            }
            if (val === 'true' || val === true) {
                return '<input type="checkbox" checked />';
            }
            if (val === 'false' || val === false) {
                return '<input type="checkbox" />';
            }
            if (Array.isArray(val)) {
                return val.map((item) => {
                    if (typeof item === 'object') {
                        return `<span>${JSON.stringify(item)}</span>`;
                    }
                    return `<span>${item}</span>`;
                }).join(', ');
            }
            if (typeof val === 'object') {
                return `<span>${JSON.stringify(val)}</span>`;
            }

            return `<span>${val}</span>`;
        },
        showInfo() {
            let content = '';
            content += `<div><span class="tag has-background-module-${this.objectData.type}">${this.objectData.type}</span> ${this.objectData.id}</div>`;
            for (const field of this.fields) {
                if (field === 'date_ranges') {
                    content += `<div><label>${this.labelsMap.get(field)}</label><div>${this.dateRanges(this.objectData.attributes.date_ranges)}</div>`;
                    continue;
                }
                if (field === 'categories') {
                    content += `<div><label>${this.labelsMap.get(field)}</label><div>${this.categories(this.objectData.attributes.categories)}</div>`;
                    continue;
                }
                content += `<div><label>${this.labelsMap.get(field)}</label><div>${this.getFieldVal(this.values[field])}</div></div>`;
            }
            BEDITA.info(content);
        }
    },
}
</script>
