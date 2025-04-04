<template>
    <div
        class="object-info mr-05"
        @click.prevent.stop="showInfo()"
    >
        <a
            :title="msgShowObjectInfo"
            class="button button-outlined-white is-small show-info"
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
            this.fields = this.fields.filter((value, index, array) => {
                return array.indexOf(value) === index;
            });

            for (const field of this.fields) {
                if (typeof field !== 'string') {
                    continue;
                }
                this.labelsMap.set(field, BEDITA_I18N?.[field] || field);
                this.values[field] = this.objectData?.relationships?.streams?.data?.[0]?.attributes?.[field]
                    || this.objectData?.relationships?.streams?.data?.[0]?.meta?.[field]
                    || this.objectData?.attributes?.[field]
                    || '-';
                if (field === 'file_size' && this.values[field] !== '-') {
                    this.values[field] = this.values[field] ? this.$helpers.formatBytes(this.values[field]) : '-';
                }
            }
            this.labelsMap.set('created', t`Created`);
            this.labelsMap.set('modified', t`Modified`);
            this.labelsMap.set('media_url', t`Media URL`);
            this.labelsMap.set('uname', t`Uname`);
            this.labelsMap.set('id', t`Id`);
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
        content(field) {
            if (field === 'categories') {
                return this.contentCategories(field);
            }
            if (field === 'date_ranges') {
                return this.contentDateRanges(field);
            }
            return `<div><label>${this.labelsMap.get(field) || field}</label><div>${this.getFieldVal(this.values[field])}</div></div>`;
        },
        contentCategories(field) {
            return `<div><label>${this.labelsMap.get(field) || field}</label><div>${this.categories(this.objectData.attributes.categories)}</div>`;
        },
        contentDateRanges(field) {
            return `<div><label>${this.labelsMap.get(field) || field}</label><div>${this.dateRanges(this.objectData.attributes.date_ranges)}</div>`;
        },
        contentMeta() {
            const meta = this.objectData?.meta;
            if (!meta) {
                return '';
            }
            let content = '<hr/><div>';
            content += `<div><label>${this.labelsMap.get('id')}</label><div>${this.getFieldVal(this.objectData.id)}</div></div>`;
            content += `<div><label>${this.labelsMap.get('uname')}</label><div>${this.getFieldVal(this.objectData.attributes.uname)}</div></div>`;
            const allowed = ['created', 'modified', 'media_url']
            for (const [key, value] of Object.entries(meta)) {
                if (!allowed.includes(key)) {
                    continue;
                }
                if (key === 'media_url') {
                    content += `<div><label>${this.labelsMap.get(key) || key}</label><div><a href="${value}" target="_blank">${value}</a></div></div>`;
                    continue;
                }
                const v = this.$helpers.formatDate(value) || value;
                content += `<div><label>${this.labelsMap.get(key) || key}</label><div>${this.getFieldVal(v)}</div></div>`;
            }
            content += '</div>';

            return content;
        },
        contentTitle() {
            return `<div><span class="tag has-background-module-${this.objectData.type}">${this.objectData.type}</span></div>`;
        },
        showInfo() {
            let content = this.contentTitle();
            for (const field of this.fields) {
                if (typeof field !== 'string') {
                    continue;
                }
                content += this.content(field);
            }
            content += this.contentMeta();
            BEDITA.info(content);
        }
    },
}
</script>
<style scoped>
.object-info .show-info {
    height: 24px;
    min-width: 24px;
    width: 24px;
    cursor: pointer;
}
</style>
