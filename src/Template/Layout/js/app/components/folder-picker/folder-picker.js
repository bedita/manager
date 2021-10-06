import { Treeselect, LOAD_ROOT_OPTIONS, LOAD_CHILDREN_OPTIONS } from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import { t } from 'ttag';
import { EventBus } from '../../directives/eventbus.js';

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

export default {

    components: {
        Treeselect,
    },

    template: `<div class="folder-picker">
        <label for="bulk-folders">${t`Folder`}</label>
        <Treeselect placeholder="" :options="options" :load-options="loadOptions" v-model="value" :disabled="disabled" />
        <input id="bulk-folders" type="hidden" name="folderSelected" :value="value" />
    </div>`,

    data() {
        return {
            options: null,
            value: null,
            disabled: true,
        };
    },

    mounted() {
        EventBus.$on('folder-picker-init', this.initOptions);
    },

    beforeDestroy() {
        EventBus.$off('folder-picker-init', this.initOptions);
    },

    methods: {
        async fetchFolders(parent) {
            const pageSize = 100;
            const folders = [];
            let filter = !parent ? 'filter[roots]' : `filter[parent]=${parent.id}`;
            const firstResponse = await fetch(`${API_URL}api/folders?${filter}&page=1&page_size=${pageSize}`, API_OPTIONS);
            const pageCount = await this.updateFolders(firstResponse, folders);
            const deferred = [];
            for (let page = 2; page <= pageCount; page++) {
                deferred.push(fetch(`${API_URL}api/folders?${filter}&page=${page}&page_size=${pageSize}`, API_OPTIONS));
            }
            const responses = await Promise.all(deferred);
            responses.forEach(async (response) => {
                await this.updateFolders(response, folders);
            });

            return folders;
        },

        async updateFolders(response, folders) {
            let json = await response.json();
            if (json.data) {
                const newFolders = json.data.map((folder) => ({id: folder.id, label: folder.attributes.title, children: null}));
                folders.push(...newFolders);
            }

            return json.meta.pagination.page_count;
        },

        async loadOptions({ action, parentNode, callback }) {
            if (action === LOAD_ROOT_OPTIONS) {
                this.options = [];
            } else if (action === LOAD_CHILDREN_OPTIONS) {
                parentNode.children = await this.fetchFolders(parentNode);
            }
        },

        async initOptions() {
            if (this.options.length > 0) {
                return;
            }
            this.options = await this.fetchFolders();
            this.disabled = false;
        },
    },
}
