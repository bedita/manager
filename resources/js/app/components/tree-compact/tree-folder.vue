<template>
    <div class="tree-folder">
        <template v-if="createNew || editMode">
            <div
                class="backdrop"
                style="display: block; z-index: 9998;"
                @click="closePanel()"
            />
            <aside
                class="main-panel-container on"
                custom-footer="true"
                custom-header="true"
            >
                <div class="main-panel fieldset">
                    <header class="mx-1 mt-1 tab tab-static unselectable">
                        <h2 v-if="createNew">{{ msgCreateNew }}</h2>
                        <h2 v-if="editMode">{{ msgEdit }}</h2>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div class="container">
                        TODO: form fields required + non required
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                :class="{'is-loading-spinner': saving}"
                                :disabled="saveDisabled"
                                @click.prevent="save"
                            >
                                <app-icon icon="carbon:save" />
                                <span class="ml-05">
                                    {{ msgSave }}
                                </span>
                            </button>
                            <button
                                class="button button-primary"
                                @click="closePanel()"
                            >
                                <app-icon icon="carbon:close" />
                                <span class="ml-05">
                                    {{ msgClose }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </template>
        <header
            class="tab"
            :class="{'open': open}"
            :open="open"
            @click.prevent.stop="toggle"
        >
            <h2>
                <span
                    class="folder-open"
                    v-if="open"
                >
                    <app-icon icon="material-symbols:folder-open" />
                </span>
                <span
                    class="folder-closed"
                    v-else
                >
                    <app-icon icon="material-symbols:folder" />
                </span>
                <template v-if="canSave()">
                    <span
                        class="editable"
                        @click.prevent.stop="editMode = true"
                        @mouseover="hoverTitle=true"
                        @mouseleave="hoverTitle=false"
                    >
                        {{ truncate(folder?.attributes?.title, 80) }}
                        <template v-if="hoverTitle">
                            <app-icon
                                icon="ph:pencil-fill"
                                color="#00aaff"
                            />
                        </template>
                    </span>
                </template>
                <template v-else>
                    <span class="not-editable">{{ truncate(folder?.attributes?.title, 80) }}</span>
                </template>
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
                <div class="object-info-container">
                    <object-info
                        border-color="transparent"
                        color="white"
                        :object-data="folder"
                    />
                </div>
                <a
                    :href="`/view/${obj?.id}`"
                    target="_blank"
                    class="mr-05"
                    v-title="msgOpenInNewTab"
                >
                    <app-icon icon="carbon:launch" />
                </a>
                <span class="modified">{{ $helpers.formatDate(folder?.meta?.modified) }}</span>
            </h2>
        </header>
        <template v-if="open">
            <div class="contents">
                <template v-if="Object.keys(subfolders)?.length">
                    <tree-folder
                        v-for="(childId, index) in Object.keys(subfolders)"
                        :can-save-map="canSaveMap"
                        :key="index"
                        :folder="folders[childId]"
                        :folders="folders || {}"
                        :subfolders="subfolders[childId]?.subfolders || {}"
                    />
                </template>
                <template v-if="children?.length">
                    <tree-content
                        v-for="(child, index) in children"
                        :can-save-map="canSaveMap"
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
        ObjectInfo: () => import(/* webpackChunkName: "object-info" */'app/components/object-info/object-info'),
        TreeContent: () => import('./tree-content.vue'),
    },
    props: {
        canSaveMap: {
            type: Object,
            required: true
        },
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
            createNew: false,
            editMode: false,
            hoverTitle: false,
            loading: false,
            msgClose: t`Close`,
            msgContents: t`Contents`,
            msgCreateContent: t`Create content`,
            msgCreateFolder: t`Create folder`,
            msgEdit: t`Edit`,
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
        canSave() {
            return this.canSaveMap?.['folders'] || false;
        },
        closePanel() {
            this.createNew = false;
            this.editMode = false;
        },
        async loadChildren() {
            try {
                this.loading = true;
                this.children = [];
                const response = await fetch(`${API_URL}tree/${this.folder.id}/children?page_size=${PAGE_SIZE}`, API_OPTIONS);
                const json = await response.json();
                if (json.error) {
                    throw new Error(json.error);
                }
                const children = json?.data?.filter(item => item?.type !== 'folders') || [];
                const pageCount = json?.meta?.pagination?.page_count || 1;
                for (let page = 2; page <= pageCount; page++) {
                    const response = await fetch(`${API_URL}tree/${this.folder.id}/children?page=${page}&page_size=${PAGE_SIZE}`, API_OPTIONS);
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
div.tree-folder aside.main-panel-container {
    z-index: 9999;
}
div.tree-folder aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
div.tree-folder button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
div.tree-folder .container {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
div.tree-folder .container > div {
    display: flex;
    flex-direction: column;
}
div.tree-folder div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
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
div.tree-folder > header > h2 > span.modified, div.tree-folder > header > h2 > a {
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
