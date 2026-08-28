import { Treeselect, LOAD_ROOT_OPTIONS, LOAD_CHILDREN_OPTIONS } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import { t } from 'ttag';

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};
const PAGE_SIZE = 100;

/**
 * Folder picker component.
 * Displays a tree-view folders list and allows to pick one.
 *
 * @prop {Boolean} disabled Enable/disable the select
 * @prop {String} initialFolder Id of the folder to pre-select
 * @prop {String} label Label to show before the select
 */
export default {
    components: {
        Treeselect,
    },

    template: `<div class="folder-picker">
        <label v-if="label"><: label :></label>
        <Treeselect
            placeholder=""
            loading-text=""
            v-model="selectedFolder"
            :default-options="initialOptions"
            :disabled="disabled"
            :options="options"
            :load-options="loadOptions"
            :auto-load-root-options="false"
            value-format="object"
            @input="onChange"
        />
        <input type="hidden" :form="form" name="folderSelected" :value="selectedFolder?.id" />
    </div>`,

    props: {
        disabled: Boolean,
        initialFolder: String,
        label: String,
        form: String,
    },

    /**
     * @property {Array} initialOptions Initial options used to pre-select the initial folder
     * @property {Array} options The list of folder to show in the select
     * @property {Object} options Currently selected folder (an object in the format {id: ..., label: ...})
     */
    data() {
        return {
            initialOptions: null,
            options: null,
            selectedFolder: null,
        };
    },

    async created() {
        if (this.initialFolder) {
            const response = await fetch(`${API_URL}tree/${this.initialFolder}/children`);
            const json = await response.json();
            const data = json.data;
            const folder = { id: data.id, label: data.attributes.title };
            this.initialOptions = [folder];
            this.selectedFolder = folder;
        }
    },

    methods: {
        async fetchFolders(parent) {
            const filter = !parent ? 'filter[roots]' : `filter[parent]=${parent.id}`;
            const firstResponse = await fetch(`${API_URL}tree?${filter}&page=1&page_size=${PAGE_SIZE}`, API_OPTIONS);
            const json = await firstResponse.json();
            const folders = [...(json?.tree?.data || [])];
            const pageCount = json?.tree?.meta?.pagination?.page_count || 1;
            for (let page = 2; page <= pageCount; page++) {
                const response = await fetch(`${API_URL}tree?${filter}&page=${page}&page_size=${PAGE_SIZE}`, API_OPTIONS);
                const json = await response.json();
                folders.push(...(json?.tree?.data || []));
            }

            return folders.map((folder) => ({ id: folder.id, label: folder.attributes.title || '(' + t`untitled` + ' ' + folder.id + ')', children: null }));
        },

        async loadOptions({ action, parentNode, callback }) {
            if (action === LOAD_ROOT_OPTIONS) {
                this.options = await this.fetchFolders(parentNode);
            } else if (action === LOAD_CHILDREN_OPTIONS) {
                parentNode.children = await this.fetchFolders(parentNode);
            }
            callback();
        },

        onChange() {
            this.$emit('change', this.selectedFolder);
        }
    },
}
