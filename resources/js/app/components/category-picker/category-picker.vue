<template>
    <div class="category-picker">
        <label
            :for="id"
            v-if="label"
        >
            {{ label }}
        </label>
        <Treeselect
            placeholder
            :form="form"
            :options="categoriesOptions"
            :disabled="disabled"
            :disable-branch-nodes="true"
            :multiple="true"
            v-model="selectedIds"
            @input="onChange"
        />
        <input
            :id="id"
            type="hidden"
            :form="form"
            name="categories"
            :value="selectedIds"
        >
    </div>
</template>
<script>
import { Treeselect } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
    components: {
        Treeselect,
    },

    props: {
        id: {
            type: String,
            default: null,
        },
        categories: {
            type: Array,
            default: () => [],
        },
        disabled: Boolean,
        label: {
            type: String,
            default: null,
        },
        form: {
            type: String,
            default: null,
        },
        initialCategories: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            categoriesOptions: [],
            selectedIds: null,
        };
    },

    mounted() {
        this.categoriesOptions = this.categories?.map((category) => ({ id: category.id, label: category.label }));
        this.selectedIds = this.initialCategories?.map(selected => this.categories.find(cat => cat.name == selected.name)?.id);
    },

    methods: {
        onChange() {
            const selectedCategories = this.categories.filter((cat) => this.selectedIds.includes(cat.id));
            this.$emit('change', selectedCategories);
        }
    },
}
</script>
