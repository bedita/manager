<template>
    <div class="object-info">
        <button
            class="button button-outlined-white is-small mr-1"
            @click.prevent="showInfo()"
        >
            <app-icon icon="carbon:information" />
            <span class="ml-05">Info</span>
        </button>
    </div>
</template>
<script>
export default {
    name: 'ObjectInfo',
    props: {
        objectData: {
            type: Object,
            required: true
        },
        propertiesConfig: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            fields: ['title', 'description'],
            labelsMap: new Map(),
        };
    },
    mounted() {
        this.$nextTick(() => {
            if (this.propertiesConfig?.[this.objectData?.type]) {
                const hasInfo = this.propertiesConfig[this.objectData.type]?.['info'];
                const hasIndex = this.propertiesConfig[this.objectData.type]?.['index'];
                if (hasInfo || hasIndex) {
                    this.fields = this.propertiesConfig[this.objectData.type]?.['info'] || this.propertiesConfig[this.objectData.type]?.['index'] || ['title', 'description'];
                }
            }
            for (const field of this.fields) {
                this.labelsMap.set(field, BEDITA_I18N?.[field] || field);
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
                return dateRange.end_date ? `<div>${dateRange.start_date} - ${dateRange.end_date}</div>` : `<div>${dateRange.start_date}</div>`;
            }).join(' ');
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
                content += `<div><label>${this.labelsMap.get(field)}</label><div>${this.objectData.attributes[field] || '-'}</div></div>`;
            }
            BEDITA.info(content);
        }
    },
}
</script>
<style scoped>
.object-info button {
    color: white;
    border: solid transparent 0px;
}
</style>
