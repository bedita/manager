/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/trees.twig
 *
 * <tree-view> component used for ModulesPage -> View
 *
 * @prop {String} objectId
 * @prop {Array} objectPaths
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
        loadOnStart: [Boolean, Number],
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

    /**
     * load content if flag set to true after component is created
     *
     * @return {void}
     */
    created() {
        this.loadTree();
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
    }
}
