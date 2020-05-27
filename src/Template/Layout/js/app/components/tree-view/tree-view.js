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
import sleep from 'sleep-promise';

export default {
    extends: RelationshipsView,
    components: {
        TreeList: () => import(/* webpackChunkName: "tree-list" */'app/components/tree-view/tree-list/tree-list'),
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

            const isChanged = !!relationsToAdd.length;
            this.$el.dispatchEvent(new CustomEvent('change', {
                bubbles: true,
                detail: {
                    id: this.$vnode.tag,
                    isChanged,
                }
            }));

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
                var t = (typeof this.loadOnStart === 'number')? this.loadOnStart : 0;
                await sleep(t);
                await this.loadObjects();
            }
        },

        async loadObjects() {
            if (this.loadOnStart) {
                const t = (typeof this.loadOnStart === 'number')? this.loadOnStart : 0;
                await sleep(t);
                const baseUrl = window.location.href;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                };
                let page = 1;
                const objects = [];
                do {
                    const response = page !== 1 ?
                        await fetch(`${baseUrl}/treeJson?page=${page}`, options) :
                        await fetch(`${baseUrl}/treeJson`, options);
                    const json = await response.json();
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

                this.objects = objects;
            }
        },

        /**
         * add an object from pendingrelations Array if not present
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
