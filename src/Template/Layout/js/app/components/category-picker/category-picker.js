import { Treeselect } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
    components: {
        Treeselect,
    },

    template: `
        <div class="category-picker">
            <label v-if="label" :for="id"><: label :></label>
            <Treeselect
                placeholder
                :options="categoriesOptions"
                :disabled="disabled"
                :disable-branch-nodes="true"
                :multiple="true"
                v-model="selectedIds"
                @input="onChange"
            />
            <input type="hidden" :id="id" name="categories" :value="selectedIds" />
        </div>
    `,

    props: {
        id: String,
        categories: Array,
        disabled: Boolean,
        label: String,
        initialCategories: Array,
    },

    data() {
        return {
            categoriesOptions: [],
            selectedIds: null,
        };
    },

    mounted() {
        this.categoriesOptions = this.categories?.map((category) => ({ id: category.id, label: category.label }));
        this.selectedIds = this.initialCategories?.map(selected => this.categories.find(cat => cat.name == selected)?.id);
    },

    methods: {
        /**
         * Emits currently selected categories on selection change.
         */
        onChange() {
            const selectedCategories = this.categories.filter((cat) => this.selectedIds.includes(cat.id));
            this.$emit('change', selectedCategories);
        }
    },
}
