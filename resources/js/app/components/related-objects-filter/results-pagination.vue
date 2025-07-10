<template>
    <div class="pagination">
        <div>
            <nav class="pagination has-text-size-smallest">
                <div class="count-items">
                    <span class="has-font-weight-bold">{{ count }}</span>
                    <span>Items</span>
                </div>
                <div class="page-size">
                    <span>Size</span>
                    <select
                        class="page-size-selector has-background-gray-700 has-border-gray-700 has-font-weight-light has-text-gray-200 has-text-size-smallest"
                        v-model="changeSize"
                        @change="update"
                    >
                        <option
                            v-for="size in options.sizes"
                            :key="size"
                            :value="size"
                        >
                            {{ size }}
                        </option>
                    </select>
                </div>
                <div
                    class="pagination-buttons"
                    v-if="pages > 1"
                >
                    <div>
                        <template v-for="page in pages">
                            <input
                                size="2"
                                class="ml-05 mr-05"
                                type="number"
                                min="1"
                                :max="pages"
                                :key="`input-${page}`"
                                v-model="changePage"
                                @keyup.enter="updatePage(changePage)"
                                @change="updatePage(changePage)"
                                v-if="page == changePage"
                            >
                            <button
                                :key="page"
                                class="button is-width-auto has-text-size-smallest"
                                :class="{ 'current-page': page === (filter?.page || 1), 'button-outlined': page !== (filter?.page || 1) }"
                                @click.prevent="updatePage(page)"
                                v-if="visiblePages.includes(page) && page !== changePage"
                            >
                                {{ page }}
                            </button>
                            <template v-if="!visiblePages.includes(page) && (page === changePage - 2 || page === changePage + 2)">
                                <span
                                    :key="page"
                                    class="pages-delimiter"
                                />
                            </template>
                        </template>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ResultsPagination',
    props: {
        count: {
            type: Number,
            default: 0,
        },
        filter: {
            type: Object,
            default: null,
        },
        options: {
            type: Object,
            default: () => ({ page: 1, pageSize: 10, sizes: [10, 20, 50, 100] }),
        },
    },
    data() {
        return {
            changePage: null,
            changeSize: null,
            pages: 1,
            visiblePages: [],
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.changePage = this.filter?.page || this.options.page || 1,
            this.changeSize = this.filter?.pageSize || this.options.pageSize || 10,
            this.pages = Math.ceil(this.count / this.changeSize);
            let visiblePages = [];
            if (this.pages <= 5) {
                visiblePages = Array.from({ length: this.pages }, (_, i) => i + 1);
                visiblePages = [...new Set(visiblePages)];
                visiblePages.sort();
                this.visiblePages = visiblePages;
                return;
            }
            // visible pages should be the first, the last, current, the previous and the next page
            visiblePages = [
                1,
                this.pages,
                this.changePage - 1,
                this.changePage,
                this.changePage + 1
            ].filter(page => page > 0 && page <= this.pages);
            visiblePages = [...new Set(visiblePages)];
            visiblePages.sort();
            this.visiblePages = visiblePages;
        });
    },
    methods: {
        update() {
            this.$emit('update', {
                page: this.changePage,
                pageSize: this.changeSize,
            });
        },
        updatePage(page) {
            this.changePage = parseInt(page);
            this.update();
        },
    },
}
</script>
