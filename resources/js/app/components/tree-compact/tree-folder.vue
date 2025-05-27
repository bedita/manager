<template>
    <div class="tree-folder">
        <header
            class="tab"
            :class="{'open': open}"
            :open="open"
            @click.prevent.stop="toggle"
        >
            <h2>
                <span class="folder-open" v-if="open">
                    <app-icon icon="material-symbols:folder-open" />
                </span>
                <span class="folder-closed" v-else>
                    <app-icon icon="material-symbols:folder" />
                </span>
                <span
                    class="title-edit"
                    v-if="editField === 'title'"
                >
                    <input
                        type="text"
                        v-model="title"
                        @click.prevent.stop="editField = 'title'"
                    >
                    <button
                        class="button button-primary"
                        @click.prevent.stop="saveTitle()"
                    >
                        <app-icon icon="carbon:save" />
                        <span class="ml-05">{{ msgSave }}</span>
                    </button>
                    <button
                        class="button button-primary"
                        @click.prevent.stop="undoTitle()"
                    >
                        <app-icon icon="carbon:reset" />
                        <span class="ml-05">{{ msgUndo }}</span>
                    </button>
                </span>
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
                <span
                    class="loader is-loading-spinner"
                    v-if="loading"
                />
                <span
                    class="tag is-smallest is-black ml-2"
                    v-if="!loading"
                >
                    {{ totalChildren }} {{ msgObjects }}
                </span>
                <span class="modified">{{ $helpers.formatDate(folder?.meta?.modified) }}</span>
            </h2>
        </header>
        <template v-if="open">
            <div class="contents">
                <template v-if="Object.keys(subfolders)?.length">
                    <tree-folder
                        v-for="(childId, index) in Object.keys(subfolders)"
                        :key="index"
                        :folder="folders[childId]"
                        :folders="folders || {}"
                        :subfolders="subfolders[childId]?.subfolders || {}"
                    />
                </template>
                <template v-if="children?.length">
                    <tree-content
                        v-for="(child, index) in children"
                        :key="index"
                        :obj="child"
                    />
                </template>
            </div>
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
const PAGE_SIZE = 100;
import { t } from 'ttag';

export default {
    name: 'TreeFolder',
    components: {
        TreeContent: () => import('./tree-content.vue'),
    },
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
            msgContents: t`Contents`,
            msgCreateContent: t`Create content`,
            msgCreateFolder: t`Create folder`,
            msgFolders: t`Folders`,
            msgObjects: t`objects`,
            msgSave: t`Save`,
            msgUndo: t`Undo`,
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
                const response = await fetch(`${API_URL}api/folders/${this.folder.id}/children?page_size=${PAGE_SIZE}`, API_OPTIONS);
                const json = await response.json();
                if (json.error) {
                    throw new Error(json.error);
                }
                const children = json?.data?.filter(item => item?.type !== 'folders') || [];
                const pageCount = json?.meta?.pagination?.page_count || 1;
                for (let page = 2; page <= pageCount; page++) {
                    const response = await fetch(`${API_URL}api/folders/${this.folder.id}/children?page=${page}&page_size=${PAGE_SIZE}`, API_OPTIONS);
                    const json = await response.json();
                    children.push(...(json?.data?.filter(item => item?.type !== 'folders') || []));
                }
                this.children = children;
                this.totalChildren = children?.length || 0;
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.loading = false;
            }
        },
        async saveTitle() {
            this.editField = null;
            this.hoverTitle = false;
            this.folder.attributes.title = this.title;
        },
        async undoTitle() {
            this.editField = null;
            this.hoverTitle = false;
            this.title = this.folder?.attributes?.title || '';
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
    padding-left: 0.5rem;
    max-width: 1000px;
}
div.tree-folder > header > h2 {
    display: flex;
    flex-direction: row;
    gap: 0.2rem;
    align-items: center;
    justify-content: start;
    border-bottom: dotted 0.1px silver;
}
div.tree-folder > header > h2 > span.editable {
    font-size: 0.875rem;
}
div.tree-folder > header > h2 > span.modified {
    font-size: 0.7rem;
}
div.tree-folder div.contents {
    border-bottom: dashed aqua 0.5px;
    margin-bottom: 1rem;
}
div.tree-folder .editable:hover {
    cursor: pointer;
    color: #00aaff;
    text-decoration: underline;
}
div.tree-folder span.loader {
    margin-left: auto;
    background-color: transparent;
}
div.tree-folder span.tag {
    margin-left: auto;
}
div.tree-folder span.folder-open, div.tree-folder span.folder-closed {
    cursor: pointer;
}
</style>
