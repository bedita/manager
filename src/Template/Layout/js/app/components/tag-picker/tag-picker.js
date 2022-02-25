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
        this.selectedIds = this.initialTags?.map((tag) => tag.id); // <= qui da sistemare | non si ha id
        this.tagsOptions = this.initialTags?.map((tag) => tag.label) || []; // <= qui da sistemare
    },

    methods: {
        update(str) {
            this.value = str;
        },
        addNewTag() {
            // TODO
            // - aggiungere il valore alla lista dei tag da salvare (it was: chiamata API per aggiungere il tag)
            // - svuotare l'input
            // - aggiungere il tag fra quelli che ha l'oggetto
            // - refresh lista dei tag esistenti
            // nota: il salvataggio è per tutto l'oggetto, con payload tipo
//             PATCH /galleries/84862
//             {
//                 "data": {
//                     "id": "84862",
//                     "type": "galleries",
//                     "attributes": {
//                         "tags": [
//                         	{
//                         		"name": "abitare"
//                         	},
//                         	{
//                         		"name": "abitare nuovo che non esiste già"
//                         	},
//                         	{
//                         		"name": "tag nuovo a caso",
//                                 "label": "TAG NUOVO A CASO"
//                         	}
//                         ]
//                     }
//                 }
//             }
        },
        onChange() {
            const selectedTags = this.initialTags.filter((tag) => this.selectedIds.includes(tag.id));
            this.$emit('change', selectedTags);
        },
        async fetchTags({ action, searchQuery, callback }) {
            if (action !== ASYNC_SEARCH || searchQuery?.length < QUERY_MIN_LENGTH) {
                return;
            }

            // TODO: da rivedere le righe seguenti, si può semplificare: non serve chiamata api.
            // l'utente inserisce quello che gli pare, il sistema salva:
            // se è nuovo, salva come nuovo, altrimenti usa tag esistente
            // del resto "/api/model/tags" non è accessibile a tutti gli utenti, per cui questa parte non funzionerebbe in alcuni casi
            const res = await fetch(`${BEDITA.base}/api/model/tags?filter[query]=${searchQuery}&page_size=100`, API_OPTIONS);
            const json = await res.json();
            const tags = [...(json.data || [])];

            const tagsOptions = tags?.map((tag) => ({ id: tag.id, label: tag.attributes.label })) || [];

            callback(null, tagsOptions);
        },
    },
}
