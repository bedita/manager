/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-list> component used for ModulesPage -> View
 *
 * @prop {String} captionField specify with field of the object is to be used a caption
 * @prop {String} childrenField specify with field of the object is to be used a children
 * @prop {Object} item object of this node
 * @prop {Array} relatedObjects list of already related Objects
 *
 */

Vue.component('tree-list', {
    name: 'tree-list',

    template: `
        <div class="tree-list-node" :class="isRoot ? 'root-node' : ''">
            <div class="node-element" v-if="!isRoot" :class="isRelated ? 'tree-related-object' : ''">
                <span
                    @click.prevent.stop="toggle"
                    class="icon"
                    :class="nodeIcon"
                    ></span>
                <input
                    type="checkbox"
                    :value="item"
                    v-model="related"
                />
                <label
                    @click.prevent.stop="toggle"
                    :class="isFolder ? 'is-folder' : ''"><: caption :></label>
            </div>
            <div :class="isRoot ? '' : 'node-children'" v-show="open" v-if="isFolder">
                <tree-list
                    @add-relation="addRelation"
                    @remove-relation="removeRelation"
                    v-for="(child, index) in item.children"
                    :key="index"
                    :item="child"
                    :related-objects="relatedObjects">
                </tree-list>
            </div>
        </div>
    `,

    props: {
        captionField: {
            type: String,
            required: false,
            default: 'name',
        },
        childrenField: {
            type: String,
            required: false,
            default: 'children',
        },
        item: {
            type: Object,
            required: true,
            default: () => {},
        },
        relatedObjects: {
            type: Array,
            default: () => [],
        }
    },

    computed: {
        /**
         * caption used in label
         *
         * @return {String}
         */
        caption() {
            return this.item[this.captionField];
        },

        /**
         * check if current node is a folder
         *
         * @return {Boolean}
         */
        isFolder() {
            return this.item.children &&
                this.item.children.length;
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
            return this.relatedObjects.filter(related => related.id === this.item.id).length;
        },

        /**
         * compute correct css class name according to this node
         *
         * @return {String} css class name
         */
        nodeIcon() {
            let css = '';
            css += this.isFolder
                ? this.open
                    ? 'icon-down-dir'
                    : 'icon-right-dir'
                : 'icon-blank'

            return css;
        }
    },

    data() {
        return {
            related: false,
            open: true,
        }
    },

    watch: {
        /**
         * watch related used as model for tree-list checkbox and triggers an event according to the state of the checkbox
         * - true: add-relation
         * - false: remove-relation
         *
         * @return {void}
         */
        related(value) {
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
         * @return {void}
         */
        toggle() {
            if (this.isFolder) {
                this.open = !this.open;
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
    }
});
