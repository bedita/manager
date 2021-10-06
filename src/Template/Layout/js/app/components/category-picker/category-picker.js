import { Treeselect } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
    components: {
        Treeselect,
    },

    template: `
        <div class="category-picker">
            <label v-if="label" :for="id"><: label :></label>
            <Treeselect placeholder :options="categoriesOptions" :disabled="disabled" :disable-branch-nodes="true" :multiple="true" v-model="value" />
            <input type="hidden" :id="id" name="categories" :value="value" />
        </div>
    `,

    props: {
        id: String,
        categories: Array,
        disabled: Boolean,
        label: String,
    },

    data() {
        return {
            categoriesOptions: [],
            value: null,
        };
    },

    mounted() {
        this.loadCategories();
    },

    methods: {
        loadCategories() {
            this.categoriesOptions = this.categories?.map((category) => ({ id: category.id, label: category.label }));
        },
    },
}
