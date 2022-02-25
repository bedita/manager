import { Treeselect, ASYNC_SEARCH } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

const API_URL = new URL(BEDITA.base).pathname;
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
            <label v-if="label" :for="id"><: label :></label>
            <Treeselect
                placeholder="Cerca tag esistenti"
                :options="tagsOptions"
                :async="true"
                :clearable="false"
                :load-options="fetchTags"
                :disabled="disabled"
                :disable-branch-nodes="true"
                :multiple="true"
                v-model="selectedIds"
                @input="onChange"
            />
            <div class="new-tag">
                <label for="new-tag">Crea un nuovo tag</label>
                <div class="input-container">
                    <input type="text" id="new-tag" :value="value" name="new-tag" @input="update($event.target.value)" />
                    <button @click.prevent="addNewTag">Aggiungi</button>
                </div>
            </div>
            <input type="hidden" :id="id" name="tags" :value="selectedIds" />
        </div>
    `,

    props: {
        id: String,
        disabled: Boolean,
        label: String,
        initialTags: Array,
    },

    data() {
        return {
            value: '',
            tagsOptions: [],
            selectedIds: [],
        };
    },

    mounted() {
        // TODO prendere initialTags da fuori, come props

        this.selectedIds = this.initialTags?.map((tag) => tag.id);
        this.tagsOptions = this.initialTags || [];
    },

    methods: {
        update(str) {
            this.value = str;
        },
        addNewTag() {
            // TODO
            // - chiamata API per aggiungere il tag
            // - svuotare l'input
            // - aggiungere il tag fra quelli che ha l'oggetto
            // - refresh lista dei tag esistenti
        },
        onChange() {
            const selectedTags = this.initialTags.filter((tag) => this.selectedIds.includes(tag.id));
            this.$emit('change', selectedTags);
        },
        async fetchTags({ action, searchQuery, callback }) {
            if (action !== ASYNC_SEARCH || searchQuery?.length < QUERY_MIN_LENGTH) {
                return;
            }

            const res = await fetch(`${BEDITA.base}/api/model/tags?filter[query]=${searchQuery}&page_size=100`, API_OPTIONS);
            const json = await res.json();
            const tags = [...(json.data || [])];

            const tagsOptions = tags?.map((tag) => ({ id: tag.id, label: tag.attributes.label })) || [];

            callback(null, tagsOptions);
        },
    },
}
