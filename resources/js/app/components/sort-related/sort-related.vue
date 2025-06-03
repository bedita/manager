<template>
    <div class="mb-05">
        <span class="mr-05">{{ msgSortBy }}</span>
        <select v-model="field">
            <option
                v-for="item in sortFields"
                :value="item.value"
                :key="item.value"
            >
                {{ item.label }}
            </option>
        </select>
        <select v-model="direction">
            <option value="asc">
                {{ msgAsc }}
            </option>
            <option value="desc">
                {{ msgDesc }}
            </option>
        </select>
        <span
            class="is-loading-spinner"
            v-if="loading"
        />
        <button
            class="button button-primary"
            :disabled="loading || !field"
            @click.prevent="save()"
            v-else
        >
            <app-icon icon="carbon:save" />
            <span class="ml-05">{{ msgSave }}</span>
        </button>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'SortRelated',
    props: {
        objectId: {
            type: String,
            required: true,
        },
        objectType: {
            type: String,
            required: true,
        },
        relationName: {
            type: String,
            required: true,
        },
        defaultField: {
            type: String,
            default: 'title',
        },
        sortFields: {
            type: Array,
            default: () => [{label: '-', value: ''}, {label: t`Title`, value: 'title'}],
        },
    },
    data() {
        return {
            dialog: null,
            direction: 'asc',
            field: '',
            loading: false,
            msgAsc: t`Ascending`,
            msgSave: t`Save`,
            msgDesc: t`Descending`,
            msgSortBy: t`Reorder by`,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.field = this.defaultField;
        });
    },
    methods: {
        async changeOrder() {
            try {
                this.loading = true;
                const url = `${BEDITA.base}/api/${this.objectType}/${this.objectId}/relationships/${this.relationName}/sort`;
                const payload = {
                    meta: {
                        field: this.field,
                        direction: this.direction,
                    }
                };
                const options = {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'content-type': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                    body: JSON.stringify(payload),
                };
                const response = await fetch(url, options);
                if (response.status === 200) {
                    this.$emit('reload-related', true);
                } else if (response.error) {
                    BEDITA.showError(response.error);
                }
            } catch (error) {
                BEDITA.showError(error);
            } finally {
                this.loading = false;
            }
        },
        async save() {
            this.dialog = BEDITA.confirm(
                t`Related items order will be changed.`,
                t`OK`,
                async () => {
                    this.dialog?.hide();
                    await this.changeOrder();
                }
            );
        },
    },
};
</script>
