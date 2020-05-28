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

    data() {
        return {
            objects: [],
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
    },

    async created() {
        if (!rootsPromise) {
            rootsPromise = this.loadObjects();
        }
        this.objects = await rootsPromise;
    },

    methods: {
        /**
         * Load tree roots.
         *
         * @return {Promise}
         */
        async loadObjects() {
            const baseUrl = window.location.href;
            const options = {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };
            let page = 1;
            let objects = [];
            do {
                let response = page !== 1 ?
                    await fetch(`${baseUrl}/treeJson?page=${page}`, options) :
                    await fetch(`${baseUrl}/treeJson`, options);
                let json = await response.json();
                if (json.data) {
                    objects.push(...json.data)
                }
                if (!json.meta ||
                    !json.meta.pagination ||
                    json.meta.pagination.page_count === json.meta.pagination.page) {
                    break;
                }
                page = json.meta.pagination.page + 1;
            } while (true);

            let children = objects
                .map((folder) => (
                    {
                        id: folder.id,
                        type: folder.type,
                        title: folder.attributes.title || folder.attributes.uname,
                        path: folder.meta.path,
                    }
                ));

            if (this.objectPaths) {
                await this.preloadPaths(children, this.objectPaths);
            }

            return children;
        },

        /**
         * Preload folders passed as object paths.
         *
         * @param {Array} roots A list of root folders.
         * @param {Array} paths A list of object paths.
         * @return {Promise}
         */
        async preloadPaths(roots, paths) {
            const baseUrl = window.location.href;
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
                        let response = await fetch(`${baseUrl}/treeJson/${currentFolder.id}?page=${page}`, options);
                        let json = await response.json();
                        folderChildren.push(
                            ...json.data.map((child) => (
                                {
                                    id: child.id,
                                    type: child.type,
                                    title: child.attributes.title || child.attributes.uname,
                                    path: child.meta.path,
                                }
                            ))
                        );
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
