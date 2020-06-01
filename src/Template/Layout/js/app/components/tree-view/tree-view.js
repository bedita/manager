let rootsPromise;

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-view> component used for ModulesPage -> View
 *
 * @prop {String} objectId
 * @prop {Array} objectPaths
 * @prop {String} relationName
 * @prop {Boolean} multipleChoice
 */
export default {
    components: {
        TreeList: () => import(/* webpackChunkName: "tree-list" */'app/components/tree-view/tree-list/tree-list'),
    },

    template: `<div class="tree-view">
        <div v-if="isLoading" class="is-loading-spinner"></div>
        <tree-list
            v-for="(child) in objects"
            :key="child.id"
            :item="child"
            :object-id="objectId"
            :object-paths="objectPaths"
            :relation-name="relationName"
            :multiple-choice="multipleChoice"
        ></tree-list>
    </div>`,

    data() {
        return {
            objects: [],
            isLoading: false,
        };
    },

    props: {
        objectId: [String, Number],
        objectPaths: Array,
        relationName: {
            type: String,
            default: 'children',
        },
        multipleChoice: {
            type: Boolean,
            default: true,
        },
        maxSize: {
            type: Number,
            default: 20,
        },
    },

    async created() {
        if (!rootsPromise) {
            rootsPromise = this.loadObjects();
        }
        this.isLoading = true;
        this.objects = await rootsPromise;
        this.isLoading = false;
    },

    methods: {
        /**
         * Load tree roots.
         *
         * @return {Promise}
         */
        async loadObjects() {
            const size = await this.checkTreeSize();
            if (size < this.maxSize) {
                return this.loadFullTree();
            }

            const baseUrl = new URL(BEDITA.base).pathname;
            const options = {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };
            let page = 1;
            let objects = [];
            do {
                let response = await fetch(`${baseUrl}treeJson?page=${page}`, options);
                let json = await response.json();
                if (json.data) {
                    objects.push(...this.transformResponse(json.data))
                }
                if (!json.meta ||
                    !json.meta.pagination ||
                    json.meta.pagination.page_count === json.meta.pagination.page) {
                    break;
                }
                page = json.meta.pagination.page + 1;
            } while (true);

            if (this.objectPaths) {
                await this.preloadPaths(objects, this.objectPaths);
            }

            return objects;
        },

        /**
         * Return the tree size (folders count).
         *
         * @return {Promise<number>}
         */
        async checkTreeSize() {
            const baseUrl = new URL(BEDITA.base).pathname;
            const options = {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };
            const response = await fetch(`${baseUrl}treeJson?full=1&page_size=1`, options);
            const json = await response.json();
            if (!json.meta || !json.meta.pagination) {
                return null;
            }
            return json.meta.pagination.count;
        },

        /**
         * Load the full tree recursively.
         *
         * @return {Promise<Array>}
         */
        async loadFullTree() {
            const baseUrl = new URL(BEDITA.base).pathname;
            const options = {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };

            const map = new Map();

            let page = 1;
            let objects = [];
            do {
                let response = await fetch(`${baseUrl}treeJson?full=1&page=${page}`, options);
                let json = await response.json();
                if (json.data) {
                    this.transformResponse(json.data, true).forEach((item) => {
                        let chunks = item.path.split('/').slice(1, -1);
                        let parentId = chunks[chunks.length - 1];
                        if (map.get(item.id)) {
                            item.children.push(...map.get(item.id).children);
                        }
                        map.set(item.id, item);
                        if (!parentId) {
                            objects.push(item);
                        } else {
                            let parentItem = map.get(parentId) || {
                                id: parentId,
                                children: [],
                            };
                            parentItem.children.push(item);
                            map.set(parentId, parentItem);
                        }
                    });
                }
                if (!json.meta ||
                    !json.meta.pagination ||
                    json.meta.pagination.page_count === json.meta.pagination.page) {
                    break;
                }
                page = json.meta.pagination.page + 1;
            } while (true);

            return objects;
        },

        /**
         * Transform response objects into tree items.
         *
         * @param {Array} objects The list of objects.
         * @param {boolean} setupChildren Should setup children array.
         * @return {Array}
         */
        transformResponse(objects, setupChildren = false) {
            return objects
                .map((folder) => (
                    {
                        id: folder.id,
                        type: folder.type,
                        title: folder.attributes.title || folder.attributes.uname,
                        path: folder.meta.path,
                        children: setupChildren ? [] : undefined,
                    }
                ));
        },

        /**
         * Preload folders passed as object paths.
         *
         * @param {Array} roots A list of root folders.
         * @param {Array} paths A list of object paths.
         * @return {Promise}
         */
        async preloadPaths(roots, paths) {
            const baseUrl = new URL(BEDITA.base).pathname;
            const options = {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };

            for (let i = 0; i < paths.length; i++) {
                let path = paths[i].split('/').slice(1, -1);
                let rootId = path.shift();
                let currentFolder = roots.find((f) => f.id == rootId);

                for (let k = 0; k < path.length; k++) {
                    let id = path[k];
                    let page = 1;
                    let folderChildren = [];
                    do {
                        let response = await fetch(`${baseUrl}treeJson/?root=${currentFolder.id}&page=${page}`, options);
                        let json = await response.json();
                        folderChildren.push(...this.transformResponse(json.data));
                        if (json.meta.pagination.page_count == page) {
                            break;
                        }
                        page++;
                    } while (true);

                    currentFolder.children = folderChildren;
                    currentFolder = folderChildren.find((f) => f.id == id);
                }
            }
        }
    }
}
