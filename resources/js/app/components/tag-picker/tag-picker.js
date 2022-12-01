import { Treeselect, ASYNC_SEARCH } from '@riophae/vue-treeselect'
import { t } from 'ttag';
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
                placeholder="${t`Search existing tags`}"
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
                @input="onChange"
            />
            <div class="new-tag" v-show="!searchonly">
                <label for="new-tag">${t`Add new tag`}</label>
                <div class="input-container">
                    <input type="text" :form="form" :value="text" id="new-tag" @input="update($event.target.value)" />
                    <button @click.prevent="addNewTag">${t`Add`}</button>
                </div>
            </div>
            <input type="hidden" :form="form" :id="id" name="tags" :value="modifiedTags" />
            <input type="hidden" :form="form" name="_types[tags]" value="json" />
        </div>
    `,

    props: {
        id: String,
        disabled: Boolean,
        label: String,
        form: String,
        initialTags: Array,
        searchonly: false,
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
        this.selectedTags = this.initialTags?.map((tag) => ({ id: tag.name, originalLabel: tag.label, label: tag.label }));
        this.parseBeforeSave();
    },

    methods: {
        onChange() {
            this.$emit('change', this.selectedTags);
        },
        parseBeforeSave() {
            this.modifiedTags = JSON.stringify(this.selectedTags.map((tag) => ({ name : tag.id, label: tag.originalLabel })));
        },
        onAdd(tag) {
            this.selectedTags.push({
                'id': tag.id,
                'originalLabel': tag.label,
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
            for (const tag of this.selectedTags) {
                if (this.text === tag.originalLabel) {
                    this.text = '';
                    return;
                }
            }
            this.selectedTags.push({
                'id': this.text,
                'label': this.text,
                'originalLabel': this.text
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

            const tagsOptions = tags?.map((tag) => ({ id: tag.attributes.name, originalLabel: tag.attributes.label, label: `${tag.attributes.label}` })) || [];

            callback(null, tagsOptions);
            this.parseBeforeSave();
        },
    },
}
