<template>
    <div class="tree-compact">
        <div
            v-for="rootId in Object.keys(tree)"
            :key="rootId"
        >
            <TreeFolder
                :folder="folders?.[rootId] || {}"
                :folders="folders || {}"
                :subfolders="tree[rootId]?.subfolders || {}"
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
export default {
    name: 'TreeCompact',
    components: {
        TreeFolder: () => import('./tree-folder.vue'),
    },
    props: {
        objectType: {
            type: String,
            required: true
        },
    },
    data() {
        return {
            loading: false,
            folders: {},
            tree: {},
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.loadTree();
        });
    },
    methods: {
        async loadTree() {
            try {
                this.loading = true;
                this.tree = [];
                const response = await fetch(`${API_URL}tree/loadAll?objectType=${this.objectType}`, API_OPTIONS);
                const json = await response.json();
                if (json.error) {
                    throw new Error(json.error);
                }
                this.tree = json?.data?.tree || [];
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
