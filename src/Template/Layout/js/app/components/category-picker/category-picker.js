import { Treeselect } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import { t } from 'ttag';

export default {

    components: {
        Treeselect,
    },

    template: `<div>
        <Treeselect placeholder="${t`Categories`}" :options="categoriesOptions" :disable-branch-nodes="true" :multiple="true" v-model="value" />
        <input type="hidden" name="categoriesSelected" :value="value" />
    </div>`,

    props: {
        id: String,
        categories: Array,
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
            this.categoriesOptions = [];
            let i = 0;
            this.categories.forEach(category => {
                this.categoriesOptions[i++] = {
                    id: category.id,
                    label: category.label
                };
            });
        },
    },
}
