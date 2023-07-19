<template>
    <nav class="pagination has-text-size-smallest">
        <div class="count-items">
            <span class="has-font-weight-bold">{{ pagination?.count }}</span>
            <span>{{ resource }}</span>
        </div>
        <div v-if="show()" class="page-size">
            <span>{{ msgSize }}</span>
            <select
                v-model="pageSize"
                form="_pagination"
                class="page-size-selector has-background-gray-700 has-border-gray-700 has-font-weight-light has-text-gray-200 has-text-size-smallest"
                @change="changePage">
                <option v-for="size in pageSizes" :key="size" :value="size">{{ size }}</option>
            </select>
        </div>
        <div v-if="show()" class="pagination-buttons">
            <div>
                <!-- first page -->
                <button v-if="pagination.page > 1" :class="pageButton" @click.prevent="changePage($event, 1)">1</button>
                <!-- delimiter -->
                <span v-if="pagination.page > 3" class="pages-delimiter"></span>
                <!-- prev page -->
                <button v-if="pagination.page > 2" :class="pageButton" @click.prevent="changePage($event, pagination.page - 1)">{{ pagination.page - 1 }}</button>
                <!-- current page -->
                <input size="2" class="ml-05" :class="pagination.page === 1 ? 'mr-05' : 'ml-05'" :value="pagination.page" @change="changePageNumber($event)" @keydown="pageKeydown($event)"/>
                <!-- next page -->
                <button v-if="pagination.page < pagination.page_count-1" :class="pageButton" @click.prevent="changePage($event, pagination.page + 1)">{{ pagination.page + 1 }}</button>
                <!-- delimiter -->
                <span v-if="pagination.page < pagination.page_count-2" class="pages-delimiter"></span>
                <!-- last page -->
                <button v-if="pagination.page < pagination.page_count" :class="pageButton" @click.prevent="changePage($event, pagination.page_count)">{{ pagination.page_count }}</button>
            </div>
        </div>
    </nav>
</template>
<script>
import { t } from 'ttag';
export default {
    name: 'PaginationNavigation',
    props: {
        pagination: {
            type: Object,
            required: true,
        },
        resource: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            msgSize: t`Size`,
            pageButton: 'has-text-size-smallest button is-width-auto button-outlined',
            pageSize: 20,
            pageSizes: [10, 20, 50, 100],
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.pageSize = this.pagination.page_size || 20;
        });
    },
    methods: {
        changePage(e, page) {
            if (page) {
                this.$emit('change-page', page);
                return;
            }
            const val = e.target.value;
            if (!val || val < 1 || val > this.pagination.page_count) {
                return;
            }
            this.$emit('change-page-size', val);
        },
        changePageNumber(e) {
            let val = e.target.value;
            val = val.trim();
            if (!val) {
                return;
            }
            val = parseFloat(val);
            if (!val || val > this.pagination.page_count) {
                e.target.value = '';

                return;
            }
            this.changePage(e, val);
        },
        pageKeydown(e) {
            if (e.key !== 'Enter' && e.keyCode !== 13) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            this.changePageNumber(e);

            return false;
        },
        show() {
            return this.pagination.count > this.pagination.page_size;
        },
    }
}
</script>
