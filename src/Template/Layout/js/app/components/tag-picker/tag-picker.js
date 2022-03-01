import { Treeselect, ASYNC_SEARCH } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};
const QUERY_MIN_LENGTH = 4;

export default {
    components: {
        Treeselect,
    },

    template: `
        <div class="tag-picker">
            <label v-if="label"><: label :></label>
            <Treeselect
                placeholder="Cerca tag esistenti"
                :options="tagsOptions"
                :async="true"
                :clearable="false"
                :load-options="fetchTags"
                :disabled="disabled"
                :disable-branch-nodes="true"
                :multiple="true"
                value-format="object"
                v-model="selectedTags"
                @select="onAdd"
                @deselect="onRemove"
            />
            <div class="new-tag">
                <label for="new-tag">Aggiungi un nuovo tag</label>
                <div class="input-container">
                    <input type="text" :value="text" id="new-tag" @input="update($event.target.value)" />
                    <button @click.prevent="addNewTag">Aggiungi</button>
                </div>
            </div>
            <input type="hidden" name="tags" :value="modifiedTags" />
            <input type="hidden" name="_types[tags]" value="json" />
        </div>
    `,

    props: {
        disabled: Boolean,
        label: String,
        initialTags: Array,
    },

    data() {
        return {
            selectedTags: [],
            modifiedTags: null,
            tagsOptions: [],
            text: '',
        };
    },

    mounted() {
        this.selectedTags = this.initialTags?.map((tag) => ({ id: tag.name, label: tag.label }));
        this.parseBeforeSave();
    },

    methods: {
        parseBeforeSave() {
            this.modifiedTags = JSON.stringify(this.selectedTags.map((tag) => ({ name : tag.id, label: tag.label })));
        },
        onAdd(tag) {
            this.selectedTags.push({
                'id': tag.id,
                'label': tag.label,
            });
            this.parseBeforeSave();
        },
        onRemove(tag) {
            const index = this.selectedTags.findIndex((t) => t.id == tag.id);
            this.selectedTags.splice(index, 1);
            this.parseBeforeSave();
        },
        update(str) {
            this.text = str;
        },
        addNewTag() {
            this.selectedTags.push({
                'id': this.text,
                'label': this.text,
            });
            this.text = '';
            this.parseBeforeSave();
        },

        async fetchTags({ action, searchQuery, callback }) {
            if (action !== ASYNC_SEARCH || searchQuery?.length < QUERY_MIN_LENGTH) {
                return callback(null, []);
            }

            const res = await fetch(`${BEDITA.base}/api/model/tags?filter[query]=${searchQuery}&page_size=30`, API_OPTIONS);
            const json = await res.json();
            const tags = [...(json.data || [])];

            const tagsOptions = tags?.map((tag) => ({ id: tag.id, label: tag.attributes.label })) || [];

            callback(null, tagsOptions);
        },
    },
}
