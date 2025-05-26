<template>
    <div class="tree-folder">
        <header
            class="tab"
            :class="{'open': open}"
            :open="open"
            @click.prevent.stop="toggle"
        >
            <h2 :class="{'is-loading-spinner': loading}">
                <template v-if="open">
                    <app-icon icon="carbon:folder-open" />
                </template>
                <template v-else>
                    <app-icon icon="carbon:folder" />
                </template>
                <template v-if="editField === 'title'">
                    <input
                        type="text"
                        v-model="title"
                        @click.prevent.stop="editField = 'title'"
                    />
                    <button @click.prevent.stop="saveTitle()">Save</button>
                </template>
                <span
                    class="editable"
                    @click.prevent.stop="editField = 'title'"
                    @mouseover="hoverTitle=true"
                    @mouseleave="hoverTitle=false"
                    v-else
                >
                    {{ folder?.attributes?.title }}
                    <template v-if="hoverTitle">
                        <app-icon icon="ph:pencil-fill" color="#00aaff" />
                    </template>
                </span>
                <span class="tag is-smallest is-black ml-2" style="float:right">
                    {{ totalChildren }}
                </span>
            </h2>
        </header>
        <template v-if="open">
            <div>
                <button
                    class="button button-outlined"
                    :disabled="show === 'subfolders'"
                    @click="show='subfolders'"
                >
                    <app-icon icon="carbon:folder" />
                    <span class="mx-05">Subfolders ({{ Object.keys(subfolders)?.length || 0 }})</span>
                </button>
                <button
                    class="button button-outlined"
                    :disabled="show === 'contents'"
                    @click="show='contents'"
                >
                    <app-icon icon="carbon:document" />
                    <span class="mx-05">Contents ({{ Object.keys(children)?.length || 0 }})</span>
                </button>
                <button
                    class="button button-outlined ml-05"
                    style="float:right;"
                >
                    <app-icon icon="carbon:add" />
                    <span class="ml-05">Create content</span>
                </button>
                <button
                    class="button button-outlined ml-05"
                    style="float:right;"
                >
                    <app-icon icon="carbon:add" />
                    <span class="ml-05">Create folder</span>
                </button>
            </div>
            <template v-if="Object.keys(subfolders)?.length && show === 'subfolders'">
                <div class="subfolders">
                    <tree-folder
                        v-for="(childId, index) in Object.keys(subfolders)"
                        :key="index"
                        :folder="folders[childId]"
                        :folders="folders || {}"
                        :subfolders="subfolders[childId]?.subfolders || {}"
                    />
                </div>
            </template>
            <template v-if="children?.length && show === 'contents'">
                <div class="children">
                    <div v-for="(child, index) in children" :key="index">
                        <div>
                            <app-icon icon="carbon:document" />
                            <span class="tag is-smallest mx-05" :class="`has-background-module-${child?.type}`">{{ child?.type }}</span>
                        </div>
                        <div>
                            <span v-title="child?.attributes?.title">{{ truncate(child?.attributes?.title, 80) }}</span>
                        </div>
                    </div>
                </div>
            </template>
        </template>
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
    name: 'TreeFolder',
    props: {
        folder: {
            type: Object,
            required: true
        },
        folders: {
            type: Object,
            required: true
        },
        subfolders: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            children: [],
            editField: null,
            hoverTitle: false,
            loading: false,
            open: false,
            show: 'subfolders',
            title: '',
            totalChildren: 0,
        }
    },
    mounted() {
        this.$nextTick(async () => {
            this.title = this.folder?.attributes?.title || '';
            await this.loadChildren();
        });
    },
    methods: {
        async loadChildren() {
            try {
                this.loading = true;
                this.children = [];
                const response = await fetch(`${API_URL}api/folders/${this.folder.id}/children?page_size=100`, API_OPTIONS);
                const json = await response.json();
                if (json.error) {
                    throw new Error(json.error);
                }
                this.children = json?.data?.filter(item => item?.type !== 'folders') || [];
                this.totalChildren = json?.meta?.pagination?.count || 0;
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.loading = false;
            }
        },
        async saveTitle() {
            console.log('Saving title:', this.title);
            this.editField = null;
            this.hoverTitle = false;
            this.folder.attributes.title = this.title;
        },
        toggle() {
            this.open = !this.open;
        },
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
    },
}
</script>
<style scoped>
div.tree-folder {
    padding: 0.5rem;
    border: dotted 0.1px silver;
    max-width: 1000px;
}
div.tree-folder > header > h2 {
}
div.tree-folder div.children {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 0.2rem;
}
div.tree-folder div.children > div {
    padding: 0.2rem;
    margin: 0.5rem;
    border: dotted 1px yellowgreen;
    width: 125px;
    height: 100px;
    font-size: x-small;
}
div.tree-folder .editable:hover {
    cursor: pointer;
    color: #00aaff;
    text-decoration: underline;
}
</style>
