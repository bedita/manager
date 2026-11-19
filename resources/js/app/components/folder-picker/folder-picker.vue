<template>
    <div class="folder-picker">
        <label v-if="label">{{ label }}</label>
        <Treeselect
            placeholder=""
            loading-text=""
            :default-options="initialOptions"
            :disabled="disabled"
            :options="options"
            :load-options="loadOptions"
            :auto-load-root-options="false"
            value-format="object"
            v-model="selectedFolder"
            @input="onChange"
        />
        <input
            type="hidden"
            :form="form"
            name="folderSelected"
            :value="selectedFolder?.id"
        >
    </div>
</template>
<script>
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

export default {
    name: 'FolderPicker',
    components: {
        Treeselect,
    },

    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
        initialFolder: {
            type: Number,
            default: null,
        },
        label: {
            type: String,
            default: null,
        },
        form: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            initialOptions: null,
            options: null,
            selectedFolder: null,
        };
    },

    async created() {
        if (this.initialFolder) {
            const response = await fetch(`${API_URL}api/folders/${this.initialFolder}`);
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
</script>
