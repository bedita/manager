<template>
    <div
        class="tree-view-node"
        :class="{'is-root': isRoot}"
        v-show="showNode"
    >
        <input
            type="hidden"
            name="_originalParents"
            :value="originalParents"
            v-if="originalParents.length > 0"
        >
        <div
            class="is-loading-spinner"
            v-if="isLoading && !parent"
        >
            <div v-if="loadingMainMessage">
                {{ loadingMainMessage }}
            </div>
            <div v-if="loadingSubMessage">
                {{ loadingSubMessage }}
            </div>
        </div>
        <div
            class="node-element py-05"
            :data-status="node?.attributes?.status"
            v-if="parent"
        >
            <label
                class="node-label"
                :class="{'icon-folder': !relationName, 'has-text-gray-550 disabled': object && node.id == object.id}"
                v-on="{ click: relationName ? () => {} : toggle }"
            >
                <input
                    :type="multipleChoice ? 'checkbox' : 'radio'"
                    :name="'relations[' + relationName + '][replaceRelated][]'"
                    :value="value"
                    :checked="isParent"
                    :class="isLocked ? 'disabled' : ''"
                    :data-folder-id="node.id"
                    @click="isLocked ? $event.preventDefault() : ''"
                    @change="toggleFolderRelation"
                    v-if="relationName"
                >
                <span v-html="nodeTitle" />
                <template v-if="isParent && node?.meta?.path && previewConfig?.[node?.meta?.path?.match(/^\/(\d+)/)?.[1]]">
                    <a
                        :href="`${preview?.url}${node.meta.path.replace('/' + node.meta.path.match(/^\/(\d+)/)?.[1], '')}`"
                        target="_blank"
                        :title="preview?.title"
                        v-for="(preview, index) in previewConfig?.[node.meta.path.match(/^\/(\d+)/)?.[1]]"
                        :key="index"
                    >
                        <app-icon
                            icon="carbon:launch"
                            :color="preview?.color || 'white'"
                        />
                    </a>
                </template>
            </label>
            <span
                v-title="node.meta.perms.roles.join(', ')"
                v-if="hasPermissionRoles"
            >
                <app-icon
                    icon="carbon:locked"
                    v-if="isLocked"
                />
                <app-icon
                    icon="carbon:unlocked"
                    v-if="!isLocked"
                />
                <app-icon
                    icon="carbon:tree-view"
                    v-if="node.meta.perms.inherited"
                />
            </span>
            <template v-if="(!node.children || node.children.length !== 0) && (!object || node.id != object.id) && showNode">
                <button
                    :class="{'is-loading-spinner': isLoading, 'icon-down-open': !isLoading && isOpen, 'icon-right-open': !isLoading && !isOpen}"
                    @click="toggle"
                />
                <div v-if="isLoading && loadingMainMessage">
                    {{ loadingMainMessage }}
                </div>
                <div v-if="isLoading && loadingSubMessage">
                    {{ loadingSubMessage }}
                </div>
            </template>
            <a
                :href="url"
                v-if="hasPermissionRoles && isLocked"
            >
                {{ msgView }}
            </a>
            <a
                :href="url"
                v-else
            >
                {{ msgEdit }}
            </a>
            <div class="tree-params">
                <div
                    class="tree-param"
                    v-if="relationName && isParent"
                >
                    <template v-if="hasPermissionRoles && isLocked">
                        <label>{{ msgMenu }}</label>
                    </template>
                    <template v-else>
                        <input
                            :id="'tree-menu-' + node.id"
                            type="checkbox"
                            :data-folder-id="node.id"
                            :checked="isMenu"
                            v-model="menu"
                            @change="toggleFolderRelationMenu"
                        >
                        <label :for="'tree-menu-' + node.id">
                            {{ msgMenu }}
                        </label>
                    </template>
                </div>
                <div
                    class="tree-param"
                    v-if="relationName && isParent && multipleChoice"
                >
                    <template v-if="hasPermissionRoles && isLocked">
                        <label>{{ msgCanonical }}</label>
                    </template>
                    <template v-else>
                        <input
                            :id="'tree-canonical-' + node.id"
                            type="checkbox"
                            :data-folder-id="node.id"
                            :checked="isCanonical"
                            v-model="canonical"
                            @change="toggleFolderRelationCanonical"
                        >
                        <label :for="'tree-canonical-' + node.id">
                            {{ msgCanonical }}
                        </label>
                    </template>
                </div>
            </div>
            <tree-slug
                :mode="relationName && isParent ? 'edit' : 'view'"
                :object-id="object.id"
                :object-type="object.type"
                :object-uname="object.uname"
                :parent-id="node?.id"
                :slug-content="node?.meta?.relation?.slug"
                :slug-path="node?.meta?.slug_path"
                :slug-path-compact="node.meta?.slug_path_compact"
                v-if="relationName && isParent"
            />
        </div>
        <div
            class="node-children"
            v-show="isOpen || !parent"
            v-if="node.children?.length > 0"
        >
            <template v-for="child in node.children">
                <tree-view
                    :key="child.id"
                    :store="store"
                    :parents="parents"
                    :parent="node"
                    :node="child"
                    :object="object"
                    :relation-name="relationName"
                    :relation-label="relationLabel"
                    :multiple-choice="multipleChoice"
                    :user-roles="userRoles"
                    :has-permissions="hasPermissions"
                    :preview-config="previewConfig"
                    :show-forbidden="showForbidden"
                    :search="search"
                    :search-in-position-active="searchInPositionActive"
                    v-if="child && child.id"
                />
            </template>
        </div>
    </div>
</template>
<script>
/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-view> component used for ModulesPage -> View
 *
 * @property {Object} store The folders store.
 * @property {string} parent The parent of the tree item.
 * @property {Object} node The model of the tree item.
 * @property {Object} object The current item to place in the tree.
 * @property {string} relationName The name of the relation to save.
 * @property {string} relationLabel The label of the relation to save.
 * @property {boolean} multipleChoice Should handle multiple relations.
 * @property {Array} parents The list of current item parents.
 */
import { PermissionEvents } from '../permission-toggle/permission-toggle.vue';
import { t } from 'ttag';

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

export default {
    name: 'TreeView',
    components: {
        TreeSlug: () => import(/* webpackChunkName: "tree-slug" */'app/components/tree-slug/tree-slug'),
    },
    props: {
        store: {
            type: Object,
            default: () => ({}),
        },
        parentsStore: {
            type: Object,
            default: () => ({}),
        },
        parent: {
            type: Object,
            default: null,
        },
        parents: {
            type: Array,
            default: () => ([]),
        },
        node: {
            type: Object,
            default: () => ({
                attributes: {
                    status: 'on',
                },
                children: [],
            }),
        },
        object: {
            type: Object,
            default: null,
        },
        relationName: {
            type: String,
            default: '',
        },
        relationLabel: {
            type: String,
            default: '',
        },
        multipleChoice: {
            type: Boolean,
            default: true,
        },
        userRoles: {
            type: Array,
            default: () => ([]),
        },
        hasPermissions: {
            type: Boolean,
            default: false,
        },
        previewConfig: {
            type: Object,
            default: () => ({}),
        },
        search: {
            type: String,
            default: '',
        },
        searchInPositionActive: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            canonical: false,
            isOpen: false,
            isLoading: false,
            loadingCounter: 0,
            loadingMainMessage: '',
            loadingSubMessage: '',
            totalCounter: 0,
            menu: false,
            msgCanonical: t`Canonical`,
            msgEdit: t`Edit`,
            msgLoadingBranchesForPosition: t`Loading branches for position`,
            msgLoadingChildrenForFolder: t`Loading children for folder`,
            msgLoadingFolder: t`Loading folder`,
            msgLoadingParentForFolder: t`Loading parent for folder`,
            msgLoadingPositionsForObject: t`Loading positions for object`,
            msgLoadingRoots: t`Loading root folders`,
            msgMenu: t`Menu`,
            msgPage: t`page`,
            msgView: t`View`,
            originalParents: [],
            showForbidden: true,
        };
    },

    async mounted() {
        if (!this.node.id) {
            this.isLoading = true;
            await this.loadRoots();
            this.isLoading = false;
            this.originalParents = this.parents.map(p => p.id);
        }
        this.isOpen = !!this.node.children;
        PermissionEvents.$on('toggle-forbidden', (value) => this.showForbidden = value);
        if (this.node.meta && !this.node.meta?.relation) {
            this.node.meta.relation = {menu: false, canonical: false};
        }
        this.canonical = this.node.meta?.relation?.canonical || false;
        this.menu = this.node.meta?.relation?.menu || false;
    },

    computed: {
        nodeTitle() {
            // ensure to escape html entities except for the search term
            let title = this.node.attributes.title;
            const div = document.createElement('div');
            div.innerHTML = title;
            title = div.innerText;
            if (!this.search) {
                return title;
            }

            return title.replace(new RegExp(`(${this.search})`, 'i'), '<span class="has-text-decoration-underline">$1</span>');
        },

        /**
         * Check if the item a root.
         *
         * @return {boolean}
         */
        isRoot() {
            if (!this.parent) {
                return false;
            }
            return !this.parent.id;
        },

        /**
         * Check if the item is the parent of the current node.
         *
         * @return {boolean}
         */
        isParent() {
            return !!this.parents.find(({ id }) => id == this.node.id);
        },

        /**
         * Check if the child item is part of the menu.
         *
         * @return {boolean}
         */
        isMenu() {
            if (!this.isParent) {
                return false;
            }
            return !!this.menu;
        },

        /**
         * Check if this is the primary position.
         *
         * @return {boolean}
         */
        isCanonical() {
            if (!this.isParent) {
                return false;
            }
            return !!this.canonical;
        },

        hasPermissionRoles() {
            const roles = this.node?.meta?.perms?.roles || [];

            return roles.length > 0
        },

        isLocked() {
            if (this.userRoles.includes('admin')) {
                return false;
            }

            const roles = this.node?.meta?.perms?.roles || [];
            if (roles.length === 0) {
                return false;
            }

            return !roles.some(item => this.userRoles.includes(item));
        },

        /**
         * The folders link.
         *
         * @return {string}
        */
        url() {
            if (!this.node) {
                return;
            }
            let API_URL = new URL(BEDITA.base).pathname;
            return `${API_URL}folders/view/${this.node.id}`;
        },

        /**
         * The input value for the current item.
         *
         * @return {string}
         */
        value() {
            return JSON.stringify({
                id: this.node?.id,
                type: this.node?.type,
                meta: {
                    relation: {
                        menu: this.menu,
                        canonical: this.canonical,
                        slug_path: this.node?.meta?.relation?.slug_path,
                    },
                },
            });
        },

        showNode() {
            if (this.search && !this.foundIn(this.node)) {
                return false;
            }

            if (this.searchInPositionActive && !this.isParent && !this.node.children) {
                return false;
            }

            if (!this.hasPermissions) {
                return true;
            }

            if (!this.isLocked) {
                return true;
            }

            if (this.isLocked && this.node.meta?.perms?.descendant_perms_granted) {
                return true;
            }

            return this.showForbidden;
        },
    },

    methods: {
        /**
         * Load tree roots.
         *
         * @return {Promise}
         */
        async loadRoots() {
            if (this.object && this.object.id && this.object.type) {
                await this.preload(this.object);
            }

            let page = 1;
            let roots = [];
            let done = false;
            do {
                this.loadingMainMessage = this.msgLoadingRoots;
                let response = await fetch(`${API_URL}tree?filter[roots]&page=${page}&page_size=100`, API_OPTIONS);
                let json = await response.json();
                json = json?.tree || {};
                if (json.data) {
                    roots.push(
                        ...json.data.map((object) =>
                            this.store[object.id] || (this.store[object.id] = object)
                        )
                    )
                }
                if (!json.meta ||
                    !json.meta.pagination ||
                    json.meta.pagination.page_count === json.meta.pagination.page) {
                    done = true;
                }
                page = json.meta.pagination.page + 1;
            } while (!done);

            this.node.children.push(...roots);
        },

        /**
         * Preload folders passed as object paths.
         *
         * @param {Object} object The lead object to preload.
         * @return {Promise}
         */
        async preload({ id, type }) {
            if (type === 'folders') {
                this.loadingMainMessage = this.msgLoadingFolder + ` ${id}`;
                const resp = await fetch(`${API_URL}tree/node/${id}`, API_OPTIONS);
                let json = await resp.json();
                let { node: folder } = json;
                this.store[folder.id] = folder;
                let parent = await this.loadParent(folder);
                if (!parent) {
                    return [];
                }
                this.parents.push(parent);
                await this.loadChildren(parent);
                return [await this.loadFolderParentRecursive(parent)];
            }

            this.loadingMainMessage = this.msgLoadingPositionsForObject + ` ${id}`;
            const response = await fetch(`${API_URL}tree/parents/${type}/${id}`);
            let json = await response.json();
            let included = json.parents || [];
            this.totalCounter = included.length;
            this.loadingCounter = 0;

            // first, store parent references with meta
            included.forEach((item) => {
                this.store[item.id] = item;
                this.parents.push(item);
            });

            // then load parent for each included
            for (let item of included) {
                item = await this.loadFolderParentRecursive(item);
                this.loadingCounter++;
                this.loadingMainMessage = this.msgLoadingBranchesForPosition + `: ${this.loadingCounter} / ${this.totalCounter}`;
            }
            this.$emit('changed-parents', this.parents);

            return Promise.all(included);
        },

        /**
         * Load folder parent.
         *
         * @param {Object} folder The folder data model.
         * @return {Promise}
         */
        async loadParent(folder) {
            if (this.parentsStore[folder.id]) {
                return this.parentsStore[folder.id];
            }
            const id = folder.id;
            this.loadingSubMessage = this.msgLoadingParentForFolder + ` ${id}`;
            const response = await fetch(`${API_URL}tree/parent/${id}`, API_OPTIONS);
            let json = await response.json();
            let object = json?.parent;
            if (!object?.id) {
                this.parentsStore[id] = null;

                return null;
            }
            this.parentsStore[id] = object;

            return this.store[object.id] || (this.store[object.id] = object);
        },

        /**
         * Load folder children.
         *
         * @param {Object} folder The folder data model.
         * @return {Promise}
         */
        async loadChildren(folder) {
            let page = 1;
            let children = [];
            let done = false;
            do {
                const id = folder.id;
                this.loadingSubMessage = this.msgLoadingChildrenForFolder + ` ${id} (` + this.msgPage + ` ${page})`;
                let childrenRes = await fetch(`${API_URL}tree?filter[parent]=${id}&page=${page}&page_size=100`, API_OPTIONS);
                let childrenJson = await childrenRes.json();
                childrenJson = childrenJson?.tree || {};
                children.push(
                    ...childrenJson.data.map((object) =>
                        this.store[object.id] || (this.store[object.id] = object)
                    )
                );
                if (childrenJson.meta.pagination.page_count == page) {
                    done = true;
                }
                page++;
            } while (!done);
            folder.children = children;
        },

        /**
         * Load folder children and parent.
         *
         * @param {Object} folder The folder data model.
         * @return {Promise}
         */
        async loadFolderParentRecursive(folder) {
            let parent;
            if (Object.keys(this.parentsStore).indexOf(folder.id) >= 0) {
                parent = this.parentsStore[folder.id];
            } else {
                parent = await this.loadParent(folder);
            }
            if (!parent) {
                return folder;
            }
            if (!this.store[parent.id].children) {
                await this.loadChildren(parent);
            }
            return await this.loadFolderParentRecursive(parent);
        },

        /**
         * Toggle children visibility
         * Fetch children if not provided.
         *
         * @param {Event} event The click event.
         * @return {Promise}
         */
        async toggle(event) {
            event.stopPropagation();
            event.preventDefault();

            this.isOpen = !this.isOpen;
            if (this.node.children || this.isLoading) {
                return;
            }

            let page = 1;
            let children = [];
            let done = false;
            this.isLoading = true;
            do {
                const id = this.node.id;
                this.loadingSubMessage = this.msgLoadingChildrenForFolder + ` ${id} (` + this.msgPage + ` ${page})`;
                let response = await fetch(`${API_URL}tree?filter[parent]=${id}&page=${page}&page_size=100`, API_OPTIONS);
                let json = await response.json();
                json = json?.tree || {};
                children.push(...json.data);
                if (json.meta.pagination.page_count == page) {
                    done = true;
                }
                page++;
            } while (!done);

            this.node.children = children;
            this.isLoading = false;
        },

        /**
         * Add/remove parent.
         *
         * @param {Event} event The input change event.
         * @return {void}
         */
        toggleFolderRelation(event) {
            this.updateChangedParents(event);
            if (this.multipleChoice) {
                let index = this.parents.findIndex(({ id }) => id == this.node.id);
                if (event.target.checked) {
                    if (index === -1) {
                        this.parents.push(this.node);
                    }
                } else if (index !== -1) {
                    this.parents.splice(index, 1);
                }
            } else {
                if (this.parents.length) {
                    this.parents.splice(0, 1);
                }
                if (event.target.checked) {
                    this.parents.push(this.node);
                }
            }
        },

        /**
         * Toggle menu param.
         *
         * @param {Event} event The input change event.
         * @return {void}
         */
        toggleFolderRelationMenu(event) {
            if (this.isParent) {
                this.updateChangedParents(event);
            }
            this.node.meta.relation.menu = event.target.checked;
        },

        /**
         * Toggle canonical param.
         *
         * @param {Event} event The input change event.
         * @return {void}
         */
        toggleFolderRelationCanonical(event) {
            if (this.isParent) {
                this.updateChangedParents(event);
            }
            this.node.meta.relation.canonical = event.target.checked;
        },

        updateChangedParents(event) {
            const folderId = event.target.attributes['data-folder-id'].value;
            const val = document.getElementById('changedParents').value.trim() || '';
            let arr = val.split(',') || [];
            arr = arr.filter((item) => item !== '');
            if (!arr.includes(folderId)) {
                arr.push(folderId);
            }
            document.getElementById('changedParents').value = arr.join(',');
            this.$emit('changed-parents', this.parents);
        },

        foundIn(item) {
            if (item?.attributes?.title && item?.attributes?.title?.toLowerCase().indexOf(this.search.toLowerCase()) !== -1) {
                return true;
            }

            if (!item?.children || item?.children.length === 0) {
                return false;
            }

            return item.children.some((child) => {
                if (child.attributes?.title?.toLowerCase().indexOf(this.search.toLowerCase()) !== -1) {
                    return true;
                }

                return this.foundIn(child);
            });
        }
    },
}
</script>
