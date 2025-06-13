<template>
    <div class="tree-folder">
        <tree-panel
            :folders="folders || {}"
            :languages="languages"
            :obj="folder"
            :object-type="'folders'"
            :schema="schema"
            :show-panel="editMode"
            :tree="tree"
            @update:showPanel="editMode = $event"
            @success="$emit('forceReload')"
            v-if="editMode && folder"
        />
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
                        class="editable folder"
                        :class="folder?.attributes?.status"
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
                    <span
                        class="folder"
                        :class="folder?.attributes?.status"
                        v-title="folder?.attributes?.title"
                    >
                        {{ truncate(folder?.attributes?.title, 80) }}
                    </span>
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
                <template v-if="canSave()">
                    <button
                        class="button button-outlined"
                        @click.prevent.stop="remove"
                    >
                        <app-icon icon="carbon:trash-can" />
                        <span class="ml-05">
                            {{ msgDelete }}
                        </span>
                    </button>
                </template>
                <div class="object-info-container">
                    <object-info
                        border-color="transparent"
                        color="white"
                        :object-data="folder"
                    />
                </div>
                <a
                    :href="`/view/${folder?.id}`"
                    target="_blank"
                    class="mr-05"
                    v-title="msgOpenInNewTab"
                    @click.prevent.stop="openNewTab(`/view/${folder?.id}`)"
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
                        v-for="childId in Object.keys(subfolders)"
                        :can-save-map="canSaveMap"
                        :key="childId"
                        :folder="folders[childId]"
                        :folders="folders || {}"
                        :languages="languages"
                        :schema="schema"
                        :subfolders="subfolders[childId]?.subfolders || {}"
                        :tree="tree"
                        @force-reload="$emit('force-reload', childId)"
                    />
                </template>
                <template v-if="children?.length">
                    <tree-content
                        v-for="child in children"
                        :can-save-map="canSaveMap"
                        :folders="folders || {}"
                        :key="child.id"
                        :languages="languages"
                        :obj="child"
                        :schema="schema"
                        :tree="tree"
                        @success="$emit('force-reload', child.id)"
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
        TreePanel: () => import('./tree-panel.vue'),
    },
    inject: ['getCSFRToken'],
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
        languages: {
            type: Object,
            default: () => {},
        },
        schema: {
            type: Object,
            default: () => ({}),
        },
        subfolders: {
            type: Object,
            default: () => ({})
        },
        tree: {
            type: Object,
            default: () => ({})
        },
    },
    data() {
        return {
            children: [],
            confirm: null,
            editMode: false,
            error: {},
            fieldsMap: {},
            fieldsAll: [],
            fieldsInvalid: [],
            fieldsOther: [],
            fieldsRequired: [],
            formFieldProperties: {},
            hoverTitle: false,
            loading: false,
            msgDelete: t`Delete`,
            msgObjects: t`objects`,
            msgOpenInNewTab: t`Open in new tab`,
            objectType: 'folders',
            open: false,
            saving: false,
            success: {},
            totalChildren: 0,
        }
    },
    computed: {
        saveDisabled() {
            return this.fieldsInvalid.length > 0 || this.saving;
        },
    },
    mounted() {
        this.$nextTick(async () => {
            this.formFieldProperties[this.objectType] = {};
            this.fieldsRequired = BEDITA?.fastCreateFields?.[this.objectType]?.required || [];
            this.fieldsAll = BEDITA?.fastCreateFields?.[this.objectType]?.fields || ['title'];
            this.fieldsAll = this.fieldsAll.map(field => {
                if (typeof field === 'object') {
                    return Object.keys(field)[0];
                }
                return field;
            });
            this.fieldsOther = this.fieldsAll.filter(field => !this.fieldsRequired.includes(field));
            const fields = BEDITA?.fastCreateFields?.[this.objectType]?.fields || [];
            let ff = fields;
            if (fields.constructor === Object) {
                ff = Object.keys(fields);
                this.fieldsMap = fields;
            }
            for (const item of ff) {
                if (item.constructor === Object) {
                    const itemKey = Object.keys(item)[0];
                    this.fieldsMap[itemKey] = item[itemKey];
                }
            }
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectType][f]);
            await this.loadChildren();
        });
    },
    methods: {
        canSave() {
            return this.canSaveMap?.['folders'] || false;
        },
        closePanel() {
            this.editMode = false;
        },
        async delete() {
            try {
                const options = {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                };
                await fetch(`${BEDITA.base}/api/folders/${this.folder.id}`, options);
                this.$emit('force-reload', this.folder.id);
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.confirm.hide();
            }
        },
        fieldError(field, val) {
            this.error[field] = val;
        },
        fieldUpdate(field, val) {
            this.formFieldProperties[this.objectType][field] = val;
            this.fieldsInvalid = this.fieldsRequired.filter(f => !this.formFieldProperties[this.objectType][f]);
        },
        fieldSuccess(field, val) {
            this.success[field] = val;
        },
        fieldKey(field) {
            return this.isNumeric(field) ? this.fieldsMap[field] : field;
        },
        fieldType(field) {
            return !this.isNumeric(field) ? this.fieldsMap[field] : null;
        },
        forceReload() {
            this.$emit('forceReload');
        },
        isNumeric(str) {
            if (typeof str != 'string') {
                return false;
            }
            return !isNaN(str) && !isNaN(parseFloat(str));
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
        openNewTab(url) {
            window.open(url, '_blank');
        },
        reloadContent(item) {
            const index = this.children.findIndex(child => child.id === item.id);
            if (index !== -1) {
                this.children.splice(index, 1, item);
            }
        },
        remove() {
            this.confirm = BEDITA.confirm(
                t`If you confirm, this resource will be gone forever. Are you sure?`,
                t`yes, proceed`,
                () => {
                    this.delete();
                }
            );
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
div.tree-folder > header > h2 > span.off, div.tree-folder > header > h2 > span.draft {
    color: #737c81;
}
div.tree-folder > header > h2 > span.folder {
    font-size: 0.875rem;
}
div.tree-folder > header > h2 > span.modified, div.tree-folder > header > h2 > a {
    font-size: 0.7rem;
}
div.tree-folder div.contents {
    border-left: dashed white 0.5px;
    border-bottom: dashed white 0.5px;
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
div.tree-folder button {
    border: solid transparent 0px;
}
</style>
