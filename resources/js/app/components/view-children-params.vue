<template>
    <div class="view-children-params params p-05 has-text-size-smaller">
        <dl class="mb-05">
            <div
                class="term-container"
                v-for="item in items"
                :key="item"
            >
                <dt>
                    {{ $helpers.humanize(item) }}
                </dt>
                <dd>
                    <span>
                        {{ format(item, getVal(related, item)) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>
</template>
<script>
import flatpickr from 'flatpickr';

export default {
    name: 'ViewChildrenParams',

    props: {
        relationSchema: {
            type: Object,
            default: () => {},
        },
        related: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            items: [],
        }
    },

    mounted() {
        this.$nextTick(() => {
            const schemaKeys = Object.keys(this.relationSchema || {});
            const paramKeys = Object.keys(this.related?.meta?.relation?.params || {});
            this.items = [...new Set([...schemaKeys, ...paramKeys])];
        });
    },

    methods: {
        format(key, value) {
            if (value === null || value === undefined || value === '') {
                return '-';
            }
            if (this.relationSchema && this.relationSchema[key]?.format === 'date-time') {
                return flatpickr.formatDate(new Date(value), 'Y-m-d h:i K');
            }

            return value;
        },
        getVal(item, key) {
            return item?.meta?.relation?.params?.[key] ?? null;
        },
    },
}
</script>
