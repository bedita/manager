import { Treeselect } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
    components: {
        Treeselect,
    },

    template: `
        <div class="tag-picker">
            <label v-if="label" :for="id"><: label :></label>
            <Treeselect
                placeholder
                :options="tagsOptions"
                :disabled="disabled"
                :disable-branch-nodes="true"
                :multiple="true"
                v-model="selectedIds"
                @input="onChange"
            />
            <input type="hidden" :id="id" name="tags" :value="selectedIds" />
        </div>
    `,

    props: {
        id: String,
        tags: Array,
        disabled: Boolean,
        label: String,
        initialTags: Array,
    },

    data() {
        return {
            tagsOptions: [],
            selectedIds: null,
        };
    },

    mounted() {
        this.tagsOptions = this.tags?.map((tag) => ({ id: tag.id, label: tag.label }));
        this.selectedIds = this.initialTags?.map(selected => this.tags.find(cat => cat.name == selected)?.id);
    },

    methods: {
        /**
         * Emits currently selected tags on selection change.
         */
        onChange() {
            const selectedTags = this.tags.filter((cat) => this.selectedIds.includes(cat.id));
            this.$emit('change', selectedTags);
        }
    },
}
