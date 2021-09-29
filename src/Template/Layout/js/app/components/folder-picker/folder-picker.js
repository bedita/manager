import { Treeselect, LOAD_ROOT_OPTIONS, LOAD_CHILDREN_OPTIONS } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
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

    template: `<div>
        <Treeselect placeholder="${t`Folder`}" :options="options" :load-options="loadOptions" v-model="value" />
        <input type="hidden" name="folderSelected" :value="value" />
    </div>`,

    props: {
        disabled: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {
            options: null,
            value: null,
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
            const folders = [];
            let filter = !parent ? 'filter[roots]' : `filter[parent]=${parent.id}`;
            let page = 1;
            let i = 0;
            do {
                let response = await fetch(`${API_URL}api/folders?${filter}&page=${page}&page_size=100`, API_OPTIONS);
                let json = await response.json();
                if (json.data) {
                    json.data.forEach(folder => {
                        folders[i++] = {id: folder.id, label: folder.attributes.title, children: null};
                    });
                }
                if (!json.meta ||
                    !json.meta.pagination ||
                    json.meta.pagination.page_count === json.meta.pagination.page) {
                    break;
                }
                page = json.meta.pagination.page + 1;
            } while (true);

            return folders;
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
        },
    },
}
