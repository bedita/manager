/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-view> component used for ModulesPage -> View
 *
 * @prop {String} relationName
 * @prop {Boolean} loadOnStart load content on component init
 * @prop {Boolean} multipleChoice
 *
 */

import RelationshipsView from 'app/components/relation-view/relationships-view/relationships-view';
import TreeList from 'app/components/tree-view/tree-list/tree-list';

export default {
    extends: RelationshipsView,
    components: {
        TreeList
    },

    props: {
        relatedObjects: {
            type: Array,
            default: () => [],
        },
        loadOnStart: [Boolean, Number],
        multipleChoice: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {
            jsonTree: {},   // json tree version of the objects list based on path
        }
    },

    /**
     * load content if flag set to true after component is created
     *
     * @return {void}
     */
    created() {
        this.loadTree();
    },

    watch: {
        /**
         * watch pendingRelations used as a model for view's checkboxes and separates relations in
         * ones to be added and ones to be removed according to the already related objects Array
         *
         * @param {Array} pendingRels
         */
        pendingRelations(pendingRels) {
            // handles relations to be added
            let relationsToAdd = pendingRels.filter((rel) => {
                return !this.isRelated(rel.id);
            });

            if (!this.multipleChoice) {
                if(relationsToAdd.length) {
                    relationsToAdd = relationsToAdd[0];
                }
            }

            this.relationsData = this.relationFormatterHelper(relationsToAdd);

            // handles relations to be removes
            let relationsToRemove = this.relatedObjects.filter((rel) => {
                return !this.isPending(rel.id);
            });

            // emit event to pass data to parent
            this.$emit('remove-relations', relationsToRemove);
        },

        /**
         * watch objects and insert already related objects into pendingRelations
         *
         * @return {void}
         */
        objects() {
            this.pendingRelations = this.objects.filter((rel) => {
                return this.isRelated(rel.id);
            });
        },
    },

    methods: {
        /**
         * check loadOnStart prop and load content if set to true
         *
         * @return {void}
         */
        async loadTree() {
            if (this.loadOnStart) {
                var t = (typeof this.loadOnStart === 'number')? this.loadOnStart : 1;
                // await sleep(t);
                await this.loadObjects();
                this.jsonTree = {
                    name: 'Root',
                    root: true,
                    object: {},
                    children: this.createTree(),
                };
            }
        },

        /**
         * add an object from prendingrelations Array if not present
         *
         * @event add-relation triggered from sub components
         *
         * @param {Object} related
         *
         * @return {void}
         */
        addRelation(related) {
            if (!related || !related.id === undefined) {
                console.error('[addRelation] needs first param (related) as {object} with property id set');
                return;
            }
            if (!this.containsId(this.pendingRelations, related.id)) {
                this.pendingRelations.push(related);
            }
        },

        /**
         * remove an object from pendingRelations Array
         *
         * @event remove-relation triggered from sub components
         *
         * @param {Object} related
         *
         * @return {void}
         */
        removeRelation(related) {
            if (!related || !related.id) {
                console.error('[removeRelation] needs first param (related) as {object} with property id set');
                return;
            }
            this.pendingRelations = this.pendingRelations.filter(pending => pending.id !== related.id);
        },

        /**
         * remove all related objects from pendingRelations Array
         *
         * @event remove-all-relations triggered from sub components
         *
         * @return {void}
         */
        removeAllRelations() {
            this.pendingRelations = [];
            this._setChildrenData(this, 'stageRelated', false);
        },

        /**
         * util function to set recursively all sub-components 'dataName' var with dataValue
         *
         * @param {Object} obj
         * @param {String} dataName
         * @param {any} dataValue
         */
        _setChildrenData(obj, dataName, dataValue) {
            if (obj !== undefined && dataName in obj) {
                obj[dataName] = dataValue;
            }

            obj.$children.forEach(child => {
                this._setChildrenData(child, dataName, dataValue);
            });
        },

        /**
         * create a json Tree from a list of objects with path
         *
         * @return {Object} json tree
         */
        createTree() {
            let jsonTree = [];
            this.objects.forEach((obj) => {
                let path = obj.meta.path && obj.meta.path.split('/');
                if (path.length) {
                    // Remove first blank element from the parts array.
                    path.shift();

                    // initialize currentLevel to root
                    let currentLevel = jsonTree;

                    path.forEach((id) => {
                        // check to see if the path already exists.
                        let existingPath = this.findPath(currentLevel, id);


                        if (existingPath) {
                            // The path to this item was already in the tree, so I set the current level to this path's children
                            currentLevel = existingPath.children;
                        } else {
                            // create a new node
                            let currentObj = obj;

                            // if current object is not the same as the discovered node get it from objects array
                            if (currentObj.id !== id) {
                                currentObj = this.findObjectById(id);
                            }

                            let newNode = {
                                id: id,
                                related: this.isRelated(id),
                                name: currentObj.attributes.title || '',
                                object: currentObj,
                                children: [],
                            };

                            currentLevel.push(newNode);
                            currentLevel = newNode.children;
                        }
                    });
                }
            });

            return jsonTree;
        },

        /**
         * check if part is already contained in the tree
         *
         * @param {Number} paths
         * @param {Number} part
         *
         * @return {Object|Boolean}
         */
        findPath(paths, part) {
            let path = paths.filter(path => path.id === part);
            return path.length ? path[0] : false;
        },

        /**
         * check if relatedObjects contains object with a specific id
         *
         * @param {Number} id
         *
         * @return {Boolean}
         */
        isRelated(id) {
            return this.relatedObjects.filter((relatedObject) => {
                return id === relatedObject.id;
            }).length ? true : false;
        },

        /**
         * check if pendingRelations contains object with a specific id
         *
         * @param {Number} id
         *
         * @return {Boolean}
         */
        isPending(id) {
            return this.pendingRelations.filter((pendingRelation) => {
                return id === pendingRelation.id;
            }).length ? true : false;
        },
    }
}
