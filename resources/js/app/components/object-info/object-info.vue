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
            cache: {},
            fields: ['title', 'description'],
            labelsMap: new Map(),
            msgShowObjectInfo: t`Show object info`,
            reloadedData: this.objectData || {},
            values: {},
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.fillData();
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
        fillData() {
            this.fields = BEDITA?.indexLists?.[this.reloadedData?.type] || ['title', 'description'];
            this.fields = this.fields?.filter((value, index, array) => {
                return array.indexOf(value) === index;
            });
            for (const field of this.fields) {
                if (typeof field !== 'string') {
                    continue;
                }
                this.labelsMap.set(field, BEDITA_I18N?.[field] || field);
                this.values[field] = this.reloadedData?.relationships?.streams?.data?.[0]?.attributes?.[field]
                    || this.reloadedData?.relationships?.streams?.data?.[0]?.meta?.[field]
                    || this.reloadedData?.attributes?.[field]
                    || this.reloadedData?.meta?.[field]
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
            return `<div><label>${this.labelsMap.get(field) || field}</label><div>${this.categories(this.reloadedData.attributes.categories)}</div>`;
        },
        contentDateRanges(field) {
            return `<div><label>${this.labelsMap.get(field) || field}</label><div>${this.dateRanges(this.reloadedData.attributes.date_ranges)}</div>`;
        },
        contentMeta() {
            const meta = this.reloadedData?.meta;
            if (!meta) {
                return '';
            }
            let content = '<hr/><div>';
            content += `<div><label>${this.labelsMap.get('id')}</label><div>${this.getFieldVal(this.reloadedData.id)}</div></div>`;
            content += `<div><label>${this.labelsMap.get('uname')}</label><div>${this.getFieldVal(this.reloadedData.attributes.uname)}</div></div>`;
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
            return `<div><span class="tag has-background-module-${this.reloadedData.type}">${this.reloadedData.type}</span></div>`;
        },
        async fetchObject(id, types) {
            const cacheKey = `${id}-${types}`;
            if (this.cache[cacheKey]) {
                return this.cache[cacheKey];
            }
            const baseUrl = new URL(BEDITA.base).pathname;
            const stringFields = this.fields.filter((field) => typeof field === 'string');
            const response = await fetch(`${baseUrl}resources/get/${id}?type=${types}&fields=${stringFields.join(',')}`, {
                credentials: 'same-origin',
                headers: {
                    accept: 'application/json',
                }
            });
            const responseJson = await response.json();
            const data = responseJson?.data || {};
            if (!data) {
                throw new Error('Object not found');
            }
            this.cache[cacheKey] = responseJson;

            return this.cache[cacheKey];
        },
        async showInfo() {
            const response = await this.fetchObject(this.objectData?.id, this.objectData?.type);
            this.reloadedData = response?.data || {};
            this.reloadedData.meta = response?.meta || {};
            this.fillData();
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
