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

import { t } from 'ttag';

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

export default {
    name: 'tree-view',

    template: `
        <div
            class="tree-view-node"
            :class="{
                'is-root': isRoot,
            }">
            <div v-if="isLoading && !parent" class="is-loading-spinner"></div>
            <div
                v-if="parent"
                class="node-element py-05"
                :data-status="node.attributes.status"
            >
                <label
                    class="node-label"
                    :class="{
                        'icon-folder': !relationName,
                        'has-text-gray-550 disabled': object && node.id == object.id,
                    }"
                    v-on="{ click: relationName ? () => {} : toggle }"
                >
                    <input
                        v-if="relationName"
                        :type="multipleChoice ? 'checkbox' : 'radio'"
                        :name="'relations[' + relationName + '][replaceRelated][]'"
                        :value="value"
                        :checked="isParent"
                        @change="toggleFolderRelation"
                    />
                    <: node.attributes.title :>
                </label>
                <button
                    v-if="(!node.children || node.children.length !== 0) && (!object || node.id != object.id)"
                    :class="{
                        'is-loading-spinner': isLoading,
                        'icon-down-open': !isLoading && isOpen,
                        'icon-right-open': !isLoading && !isOpen,
                    }"
                    @click="toggle"
                ></button>
                <a :href="url">${t`edit`}</a>
                <div class="tree-params">
                    <div
                        v-if="relationName && isParent"
                        class="tree-param"
                    >
                        <input
                            :id="'tree-menu-' + node.id"
                            type="checkbox"
                            :checked="isMenu"
                            @change="toggleFolderRelationMenu"
                        />
                        <label :for="'tree-menu-' + node.id">
                            ${t`Menu`}
                        </label>
                    </div>
                    <div
                        v-if="relationName && isParent && multipleChoice"
                        class="tree-param"
                    >
                        <input
                            :id="'tree-canonical-' + node.id"
                            name="_changedCanonical"
                            type="radio"
                            :checked="isCanonical"
                            @change="toggleFolderRelationCanonical"
                        />
                        <label :for="'tree-canonical-' + node.id">
                            ${`Canonical`}
                        </label>
                    </div>
                </div>
            </div>
            <div class="node-children" v-show="isOpen || !parent">
                <tree-view
                    v-for="(child, index) in node.children"
                    :store="store"
                    :parents="parents"
                    :parent="node"
                    :key="index"
                    :node="child"
                    :object="object"
                    :relation-name="relationName"
                    :relation-label="relationLabel"
                    :multiple-choice="multipleChoice"
                ></tree-view>
            </div>
        </div>
    `,

    props: {
        store: {
            type: Object,
            default: () => ({}),
        },
        parent: Object,
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
        object: Object,
        relationName: String,
        relationLabel: String,
        multipleChoice: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {
            isOpen: false,
            isLoading: false,
        };
    },

    computed: {
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
            return !!this.node.meta?.relation?.menu;
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
            return !!this.node.meta?.relation?.canonical;
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
            let menu = false;
            if (this.node.meta.relation && ('menu' in this.node.meta.relation)) {
                menu = !!this.node.meta.relation.menu;
            }
            let canonical = false;
            if (this.node.meta.relation && ('canonical' in this.node.meta.relation)) {
                canonical = !!this.node.meta.relation.canonical;
            }
            return JSON.stringify({
                id: this.node.id,
                type: this.node.type,
                meta: {
                    relation: {
                        menu,
                        canonical,
                    },
                },
            });
        },
    },

    async mounted() {
        if (!this.node.id) {
            this.isLoading = true;
            await this.loadRoots();
            this.isLoading = false;
        }
        this.isOpen = !!this.node.children;
    },

    methods: {
        /**
         * Load tree roots.
         *
         * @return {Promise}
         */
        async loadRoots() {
            if (this.object && this.object.id) {
                await this.preload(this.object);
            }

            let page = 1;
            let roots = [];
            let done = false;
            do {
                let response = await fetch(`${API_URL}api/folders?filter[roots]&page=${page}&page_size=100`, API_OPTIONS);
                let json = await response.json();
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
            if (!type) {
                let response = await fetch(`${API_URL}api/objects/${id}`, API_OPTIONS);
                let json = await response.json();
                type = json.data.type;
            }

            if (type === 'folders') {
                let response = await fetch(`${API_URL}api/folders/${id}`, API_OPTIONS)
                let { data: folder } = await response.json();
                this.store[folder.id] = folder;
                let parent = await this.loadParent(folder);
                if (!parent) {
                    return [];
                }
                this.parents.push(parent);
                await this.loadChildren(parent);
                return [await this.loadFolderParentRecursive(parent)];
            }

            let response = await fetch(`${API_URL}api/${type}/${id}?include=parents`);
            let json = await response.json();
            let included = json.included || [];
            return await Promise.all(
                included.map(async (folder) => {
                    this.store[folder.id] = folder;
                    this.parents.push(folder);
                    return this.loadFolderParentRecursive(folder);
                })
            );
        },

        /**
         * Load folder parent.
         *
         * @param {Object} folder The folder data model.
         * @return {Promise}
         */
        async loadParent(folder) {
            let response = await fetch(`${API_URL}api/folders/${folder.id}/parent`, API_OPTIONS);
            let json = await response.json();
            let { data: object } = json;
            if (!object) {
                return null;
            }
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
                let childrenRes = await fetch(`${API_URL}api/folders?filter[parent]=${folder.id}&page=${page}`, API_OPTIONS);
                let childrenJson = await childrenRes.json();
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
            let parent = await this.loadParent(folder);
            if (parent) {
                if (!parent.children) {
                    await this.loadChildren(parent);
                }
                return await this.loadFolderParentRecursive(parent);
            }

            return folder;
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
                let response = await fetch(`${API_URL}api/folders?filter[parent]=${this.node.id}&page=${page}`, API_OPTIONS);
                let json = await response.json();
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
            document.getElementById('changedParents').value = 1;
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
                document.getElementById('changedParents').value = 1;
            }

            let relation = this.node.meta.relation || {};
            relation.menu = event.target.checked;
        },

        /**
         * Toggle canonical param.
         *
         * @param {Event} event The input change event.
         * @return {void}
         */
        toggleFolderRelationCanonical(event) {
            if (this.isParent) {
                document.getElementById('changedParents').value = 1;
            }

            let relation = this.node.meta.relation || {};
            relation.canonical = event.target.checked;
        },
    },
}
