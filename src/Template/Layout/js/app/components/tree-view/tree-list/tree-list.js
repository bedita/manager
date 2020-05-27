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
 * @prop {Boolean} multipleChoice (default true)
 * @prop {Object} item object of this node
 * @prop {Array} relatedObjects list of already related Objects
 *
 */

export default {
    name: 'tree-list',

    template: `
        <div
            class="tree-list-node"
            :class="treeListMode">

            <div v-if="!isRoot">
                <div v-if="multipleChoice"
                    class="node-element"
                    :class="{
                        'tree-related-object': isRelated,
                        'disabled': isCurrentObjectInPath,
                        'node-folder': isFolder,
                    }">

                    <input
                        type="checkbox"
                        :value="item"
                        v-model="related"
                    />
                    <label
                        :class="isFolder ? 'is-folder' : ''"><: caption :></label>
                    <button
                        v-if="isFolder && (!children || children.length !== 0)"
                        :class="nodeClasses"
                        @click.prevent.stop="toggle"
                        ></button>
                </div>
                <div v-else class="node-element"
                    :class="{
                        'tree-related-object': isRelated || stageRelated,
                        'was-related-object': isRelated && !stageRelated,
                        'disabled': isCurrentObjectInPath
                    }"

                    @click.prevent.stop="select">
                    <label><: caption :></label>
                    <button
                        v-if="isFolder && (!children || children.length !== 0)"
                        :class="nodeClasses"
                        @click.prevent.stop="toggle"
                        ></button>
                </div>
            </div>
            <div :class="isRoot ? '' : 'node-children'" v-show="open" v-if="isFolder">
                <tree-list
                    @add-relation="addRelation"
                    @remove-relation="removeRelation"
                    @remove-all-relations="removeAllRelations"
                    v-for="(child, index) in children"
                    :key="index"
                    :item="child"
                    :multiple-choice="multipleChoice"
                    :related-objects="relatedObjects"
                    :object-id=objectId>
                </tree-list>
            </div>
        </div>
    `,

    data() {
        return {
            stageRelated: false,
            related: false,
            open: false,
            isLoading: false,
            children: undefined,
        }
    },

    props: {
        multipleChoice: {
            type: Boolean,
            default: true,
        },
        item: {
            type: Object,
            required: true,
            default: () => {},
        },
        relatedObjects: {
            type: Array,
            default: () => [],
        },
        objectId: {
            type: String,
            required: false,
        },
    },

    computed: {
        /**
         * caption used in label
         *
         * @return {String}
         */
        caption() {
            return this.item.attributes.title || this.item.attributes.uname;
        },

        /**
         * check if current node is a folder
         *
         * @return {Boolean}
         */
        isFolder() {
            return this.item.type === 'folders';
        },

        /**
         * check if current node is a root node
         *
         * @return {Boolean}
         */
        isRoot() {
            return this.item.root || false;
        },

        /**
         * check if current node is already related
         *
         * @return {Boolean}
         */
        isRelated() {
            if (!this.item.id) {
                return false;
            }

            return !!this.relatedObjects.filter(related => related.id === this.item.id).length;
        },

        /**
         * check if current object is in item path
         */
        isCurrentObjectInPath() {
            return this.item && this.item.meta.path && this.item.meta.path.includes(this.objectId);
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
                ? this.open
                    ? 'icon-down-open'
                    : 'icon-right-open'
                : 'unicode-branch'

            return css;
        },

        /**
         * compute correct css class name according to this node
         *
         * @return {String} css class name
         */
        treeListMode() {
            let css = [];
            if (this.isRoot) {
                css.push('root-node');
            }

            if (!this.multipleChoice) {
                css.push('tree-list-single-choice');
            } else {
                css.push('tree-list-multiple-choice');
            }

            if (this.isCurrentObject) {
                css.push('disabled');
            }

            return css.join(' ');
        }
    },

    watch: {
        /**
         * watch related used as model for tree-list in multiple-choice mode, used as model for checkboxes
         * set the stageRelated value
         *
         * @return {void}
         */
        related(value) {
            this.stageRelated = value;
        },

        /**
         * watch stageRelated used as model for tree-list in single-choice mode and triggers an event according to the state of t
         * - true: add-relation
         * - false: remove-relation
         *
         * @return {void}
         */
        stageRelated(value) {
            if (!this.item.object) {
                return;
            }
            if (value) {
                this.$emit('add-relation', this.item.object);
            } else {
                this.$emit('remove-relation', this.item.object);
            }
        },

        /**
         * watch relatedObjects and check if is already related
         *
         * @return {void}
         */
        relatedObjects() {
            this.related = this.isRelated;
        },
    },

    methods: {
        /**
         * toggle children visibility
         *
         * @return {Promise}
         */
        async toggle() {
            if (this.isFolder) {
                this.open = !this.open;
            }
            if (!this.children && !this.isLoading) {
                this.isLoading = true;
                const baseUrl = window.location.href;
                const options = {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                };
                const response = await fetch(`${baseUrl}/treeJson/${this.item.id}?filter[type]=folders`, options);
                const json = await response.json();
                const children = this.children = this.children || [];
                children.push(...json.data);
                this.isLoading = false;
            }
        },

        /**
         * triggers add-relation event in order to pass object to upper component
         * tree-view handles the addition
         *
         * @param {Object} rel
         *
         * @return {void}
         */
        addRelation(rel) {
            this.$emit('add-relation', rel);
        },

        /**
         * triggers remove-relation event in order to pass object to upper component
         * tree-view handles the removal
         *
         * @param {Object} rel
         *
         * @return {void}
         */
        removeRelation(rel) {
            this.$emit('remove-relation', rel);
        },

        /**
         * triggers remove-all-relations event in order to remove all pending relations
         *
         * @return {void}
         */
        removeAllRelations() {
            this.$emit('remove-all-relations');
        },

        /**
         * single-choice mode: select current tree entry for staging
         *
         * @return {void}
         */
        select() {
            // avoid user selecting same tree item (or in path) as current object
            if (this.isCurrentObjectInPath) {
                return;
            }
            // TO-DO handle folder removal from tree or folder as root

            // let oldValue = this.stageRelated;
            this.$emit('remove-all-relations');
            // if (oldValue) {
            //     this.$emit('add-relation', { id: null, type: 'folders'});
            // }
            this.stageRelated = !this.stageRelated;
        }
    }
}
