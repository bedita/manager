const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-view> component used for ModulesPage -> View
 *
 * @TODO
 * JSdoc
 * replace objectId and objectType with model object
 * style/template review
 */
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
                :data-status="node.attributes.status">
                <label
                    class="node-label"
                    :class="{
                        'icon-folder': !relationName,
                        'has-text-gray-550 disabled': node.id == objectId,
                    }"
                    v-on="{ click: relationName ? () => {} : toggle }"
                >
                    <input
                        v-if="relationName"
                        :type="multipleChoice ? 'checkbox' : 'radio'"
                        :name="'relations[' + relationName + '][replaceRelated][]'"
                        :value="nodeValue"
                        :checked="isParent"
                        @change="toggleFolderRelation"
                    />
                    <: node.attributes.title :>
                </label>
                <div
                    v-if="relationName && isParent"
                    class="tree-params">
                    <label>
                        <input
                            type="checkbox"
                            :checked="isMenu"
                            @change="toggleFolderRelationMenu"
                        />
                        <: t('Menu') :>
                    </label>
                </div>
                <button
                    v-if="(!node.children || node.children.length !== 0) && node.id != objectId"
                    :class="{
                        'is-loading-spinner': isLoading,
                        'icon-down-open': !isLoading && isOpen,
                        'icon-right-open': !isLoading && !isOpen,
                    }"
                    @click="toggle"
                ></button>
                <a :href="url"><: t('edit') :></a>
            </div>
            <div class="node-children" v-show="isOpen || !parent">
                <tree-view
                    v-for="(child, index) in node.children"
                    :parents="parents"
                    :parent="node"
                    :key="index"
                    :node="child"
                    :object-type="objectType"
                    :object-id="objectId"
                    :relation-name="relationName"
                    :multiple-choice="multipleChoice"
                ></tree-view>
            </div>
        </div>
    `,

    props: {
        parents: {
            type: Array,
            default: () => ([]),
        },
        objectType: String,
        objectId: [String, Number],
        relationName: String,
        multipleChoice: {
            type: Boolean,
            default: true,
        },
        parent: Object,
        node: {
            type: Object,
            default: () => ({
                attributes: {
                    status: 'on',
                },
                children: [],
            }),
        },
    },

    data() {
        return {
            isOpen: false,
            isLoading: false,
        };
    },

    computed: {
        isRoot() {
            if (!this.parent) {
                return false;
            }
            return !this.parent.id;
        },

        isParent() {
            return !!this.parents.find(({ id }) => id == this.node.id);
        },

        isMenu() {
            if (!this.isParent) {
                return false;
            }
            if (!this.node.meta.relation) {
                return true;
            }
            return !!this.node.meta.relation.menu;
        },

        /**
         * The folders link.
         *
         * @return {String}
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
        nodeValue() {
            let menu = true;
            if (this.node.meta.relation && ('menu' in this.node.meta.relation)) {
                menu = !!this.node.meta.relation.menu;
            }
            return JSON.stringify({
                id: this.node.id,
                type: this.node.type,
                meta: {
                    relation: {
                        menu,
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
        async loadRoots() {
            let parents = [];
            if (this.objectId) {
                parents = await this.preload(this.objectId, this.objectType);
            }

            let page = 1;
            let roots = [];
            do {
                let response = await fetch(`${API_URL}api/folders?filter[roots]&page=${page}`, API_OPTIONS);
                let json = await response.json();
                if (json.data) {
                    roots.push(
                        ...json.data.map((object) => parents.find((parent) => parent.id == object.id) || object)
                    )
                }
                if (!json.meta ||
                    !json.meta.pagination ||
                    json.meta.pagination.page_count === json.meta.pagination.page) {
                    break;
                }
                page = json.meta.pagination.page + 1;
            } while (true);

            this.node.children.push(...roots);
        },

        /**
         * Preload folders passed as object paths.
         *
         * @param {string} id The leaf object id.
         * @return {Promise}
         */
        async preload(id, type) {
            if (!type) {
                let response = await fetch(`${API_URL}api/objects/${id}`, API_OPTIONS);
                let json = await response.json();
                type = json.data.type;
            }

            if (type === 'folders') {
                let response = await fetch(`${API_URL}api/folders/${id}`, API_OPTIONS)
                let { data: folder } = await response.json();
                let parent = await this.loadParent(folder);
                if (!parent) {
                    return [];
                }
                this.parents.push(parent);
                return [await this.loadFolder(parent, folder)];
            }

            let response = await fetch(`${API_URL}api/${type}/${id}?include=parents`);
            let json = await response.json();
            return await Promise.all(
                json.included.map(async (folder) => {
                    this.parents.push(folder);
                    return this.loadFolder(folder, false);
                })
            );
        },

        async loadParent(folder) {
            let response = await fetch(`${API_URL}api/folders/${folder.id}/parent`, API_OPTIONS);
            let json = await response.json();

            return json.data;
        },

        async loadFolder(folder, child) {
            let page = 1;
            if (child !== false) {
                let children = [];
                do {
                    let childrenRes = await fetch(`${API_URL}api/folders?filter[parent]=${folder.id}&page=${page}`, API_OPTIONS);
                    let childrenJson = await childrenRes.json();
                    children.push(
                        ...childrenJson.data.map((object) => {
                            if (child && child.id == object.id) {
                                return child;
                            }
                            return object;
                        })
                    );
                    if (childrenJson.meta.pagination.page_count == page) {
                        break;
                    }
                    page++;
                } while (true);
                folder.children = children;
            }

            let parent = await this.loadParent(folder);
            if (parent) {
                return await this.loadFolder(parent, folder);
            }

            return folder;
        },

        /**
         * Toggle children visibility
         * Fetch children if not provided.
         *
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
            this.isLoading = true;
            do {
                let response = await fetch(`${API_URL}api/folders?filter[parent]=${this.node.id}&page=${page}`, API_OPTIONS);
                let json = await response.json();
                children.push(...json.data);
                if (json.meta.pagination.page_count == page) {
                    break;
                }
                page++;
            } while (true);

            this.node.children = children;
            this.isLoading = false;
        },

        toggleFolderRelation(event) {
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

        toggleFolderRelationMenu(event) {
            let relation = this.node.meta.relation || {};
            relation.menu = event.target.checked;
        },
    },
}
