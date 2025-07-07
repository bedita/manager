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
                        <button
                            v-for="page in pages"
                            :key="page"
                            class="button is-width-auto has-text-size-smallest"
                            :class="{ 'current-page': page === (filter?.page || 1), 'button-outlined': page !== (filter?.page || 1) }"
                            @click.prevent="updatePage(page)"
                        >
                            {{ page }}
                        </button>
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
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.changePage = this.filter?.page || this.options.page || 1,
            this.changeSize = this.filter?.pageSize || this.options.pageSize || 10,
            this.pages = Math.ceil(this.count / this.changeSize);
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
            this.changePage = page;
            this.update();
        },
    },
}
</script>
