/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-list> component used for ModulesPage -> View
 *
 * modes:
 * - single-choice tree
 * - multiple-choice tree
 *
 * @prop {Object} item object of this node
 * @prop {String} objectId the id of the object to insert in the tree
 * @prop {Array} objectPaths the current paths of the object to insert in the tree
 * @prop {String} relationName the relation to save
 * @prop {Boolean} multipleChoice (default true)
 */
export default {
    name: 'tree-list',

    template: `
        <div class="tree-list-node">
            <div
                class="node-element"
                :class="{
                    'node-folder': isFolder,
                }">

                <label :class="isFolder ? 'is-folder' : ''">
                    <input
                        v-if="relationName"
                        :checked="isChecked"
                        :disabled="isDisabled"
                        :type="multipleChoice ? 'checkbox' : 'radio'"
                        :name="'relations[' + relationName + '][replaceRelated][]'"
                        :value="itemValue"
                    />
                    <: item.title :>
                </label>
                <button
                    v-if="isFolder && (!item.children || item.children.length !== 0)"
                    :class="nodeClasses"
                    @click.prevent.stop="toggle"
                    ></button>
            </div>
            <div class="node-children" v-show="isOpen" v-if="isFolder">
                <tree-list
                    v-for="(child, index) in item.children"
                    :key="index"
                    :item="child"
                    :object-id="objectId"
                    :object-paths="objectPaths"
                    :relation-name="relationName"
                    :multiple-choice="multipleChoice"
                ></tree-list>
            </div>
        </div>
    `,

    data() {
        return {
            related: false,
            isOpen: false,
            isLoading: false,
        }
    },

    props: {
        item: {
            type: Object,
            required: true,
            default: () => {},
        },
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

    computed: {
        /**
         * check if current node is a folder
         *
         * @return {Boolean}
         */
        isFolder() {
            return this.item.type === 'folders';
        },

        /**
         * Check if the input should be checked.
         *
         * @return {Boolean}
         */
        isChecked() {
            if (!this.objectPaths || !this.item) {
                return false;
            }
            return this.objectPaths.some((path) => path.split('/').slice(0, -1).pop() == this.item.id);
        },

        /**
         * Check if the input should be disabled.
         *
         * @return {Boolean}
         */
        isDisabled() {
            if (!this.objectPaths || !this.item) {
                return true;
            }
            if (this.item.id == this.objectId) {
                return true;
            }
            if (this.multipleChoice) {
                return false;
            }
            return this.objectPaths.some((path) => this.item.path.startsWith(path));
        },

        /**
         * The input value for the current item.
         *
         * @return {String}
         */
        itemValue() {
            if (!this.item) {
                return '';
            }
            return JSON.stringify({ id: this.item.id, type: this.item.type });
        },

        /**
         * compute correct icon css class name according to this node
         *
         * @return {String} css class name
         */
        nodeClasses() {
            let css = '';
            if (this.isLoading) {
                return 'is-loading-spinner';
            }
            css += this.isFolder
                ? this.isOpen
                    ? 'icon-down-open'
                    : 'icon-right-open'
                : 'unicode-branch'

            return css;
        },
    },

    /**
     * Open the folder if children are provided.
     *
     * @return {void}
     */
    created() {
        if (this.item && this.item.children) {
            this.isOpen = true;
        }
    },

    methods: {
        /**
         * Toggle children visibility
         * Fetch children if not provided.
         *
         * @return {Promise}
         */
        async toggle() {
            this.isOpen = !this.isOpen;
            if (this.item.children || this.isLoading) {
                return;
            }

            this.isLoading = true;
            const baseUrl = window.location.href;
            const options = {
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                }
            };
            let page = 1;
            let children = [];
            do {
                let response = await fetch(`${baseUrl}/treeJson/?root=${this.item.id}&page=${page}`, options);
                let json = await response.json();
                children.push(
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
            this.item.children = children;
            this.isFetched = true;
            this.isLoading = false;
        },
    }
}
