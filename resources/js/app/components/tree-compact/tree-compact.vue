<template>
    <div class="tree-compact">
        <tree-panel
            :can-save-map="canSaveMap"
            :folders="folders || {}"
            :languages="languages"
            :obj="{}"
            :object-type="'folders'"
            :schema="schema"
            :show-panel="newFolder"
            :tree="tree"
            @update:showPanel="newFolder = $event; newContent = false"
            @success="loadTree"
            v-if="newFolder"
        />
        <tree-panel
            :can-save-map="canSaveMap"
            :folders="folders || {}"
            :languages="languages"
            :obj="{}"
            :object-type="'choose'"
            :schema="schema"
            :show-panel="newContent"
            :tree="tree"
            @update:showPanel="newContent = $event; newFolder = false"
            @success="loadTree"
            v-if="newContent"
        />
        <template v-if="canSave()">
            <div class="buttons">
                <button
                    class="button button-outlined"
                    @click.prevent="newFolder = true"
                >
                    <app-icon icon="carbon:folder-add" />
                    <span class="ml-05">{{ msgNewFolder }}</span>
                </button>
                <button
                    class="button button-outlined"
                    @click.prevent="newContent = true"
                >
                    <app-icon icon="carbon:document-add" />
                    <span class="ml-05">{{ msgNewContent }}</span>
                </button>
            </div>
        </template>
        <div
            v-for="rootId in Object.keys(tree)"
            :key="rootId"
        >
            <tree-folder
                :can-save-map="canSaveMap"
                :folder="folders?.[rootId] || {}"
                :folders="folders || {}"
                :languages="languages"
                :schema="schema"
                :subfolders="tree[rootId]?.subfolders || {}"
                :tree="tree"
                @force-reload="forceReload"
            />
        </div>
    </div>
</template>
<script>
const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    method: 'GET',
    credentials: 'same-origin',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-Token': BEDITA.csrfToken,
    },
};
import { t } from 'ttag';
export default {
    name: 'TreeCompact',
    components: {
        TreeFolder: () => import('./tree-folder.vue'),
        TreePanel: () => import('./tree-panel.vue'),
    },
    props: {
        canSaveMap: {
            type: Object,
            required: true
        },
        languages: {
            type: Object,
            default: () => {},
        },
        schema: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            loading: false,
            folders: {},
            msgNewContent: t`New content`,
            msgNewFolder: t`New folder`,
            newContent: false,
            newFolder: false,
            tree: {},
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.loadTree();
        });
    },
    methods: {
        canSave() {
            return this.canSaveMap?.['folders'] || false;
        },
        forceReload() {
            this.loadTree(true);
        },
        async loadTree(noCache) {
            try {
                this.loading = true;
                this.tree = {};
                const response = await fetch(`${API_URL}tree/loadAll?objectType=folders${noCache ? '&no_cache=true' : ''}`, API_OPTIONS);
                const json = await response.json();
                if (json.error) {
                    throw new Error(json.error);
                }
                this.tree = json?.data?.tree || {};
                this.folders = json?.data?.folders || [];
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.loading = false;
            }
        },
    },
}
</script>
<style scoped>
.tree-compact {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}
.tree-compact > div.buttons {
    margin-left: 0.5rem;
}
.tree-compact > div.buttons {
    margin-bottom: 0.5rem;
}
.tree-compact > div.buttons > button > span {
    font-size: 0.7rem;
}
</style>
