/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relationships-view> component used for ModulesPage -> View
 *
 */

import { PaginatedContentMixin } from 'app/mixins/paginated-content';

export default {
    mixins: [ PaginatedContentMixin ],

    // props used by parent
    props: {
        relationName: {
            type: String,
            required: true,
        },
        relationLabel: {
            type: String,
            required: true,
        },
        viewVisibility: {
            type: Boolean,
            default: () => false,
        },
        addedRelations: {
            type: Array,
            default: () => [],
        },
        // relations already related to the object
        hideRelations: {
            type: Array,
            default: () => [],
        }
    },

    computed: {
        keyEvents() {
            return {
                'esc': {
                    keyup: this.handleKeyboard,
                }
            };
        },
    },

    data() {
        return {
            method: 'relationships',    // define AppController method to be used
            loading: false,
            pendingRelations: [],           // pending elements to be added
            relationsData: [],              // hidden field containing serialized json passed on form submit
            isVisible: false,
        }
    },

    /**
     * setup correct endpoint for PaginatedContentMixin.getPaginatedObjects()
     *
     * @return {void}
     */
    created() {
        this.endpoint = `${this.method}/${this.relationName}`;
    },

    watch: {
        /**
         * watch elements staged from relation-view (view can delete last added elements before saving)
         *
         * @param {Array} relations
         *
         * @return {void}
         */
        addedRelations(relations) {
            this.pendingRelations = relations;
        },

        /**
         * watch pendingRelations Array and prepare it for saving using serialized json input field
         *
         * @param {Array} relations
         *
         * @return {void}
         */
        pendingRelations(relations) {
            this.relationsData = this.relationFormatterHelper(relations);
        },

        /**
         * set isVisible value from property's viewVisibility change
         *
         * @param {Boolean} value
         *
         * @return {void}
         */
        viewVisibility(value) {
            this.isVisible = value;
        },

        /**
         * emit event to parent
         * load data when empty
         * @event 'visibility-setter'
         *
         * @return {void}
         */
        isVisible() {
            if (!this.objects.length) {
                this.loadObjects();
            }
            // avoid problem with vue rendering queue
            this.$nextTick( () => {
                if (this.isVisible && this.$refs.inputFilter) {
                    this.$refs.inputFilter.focus();
                }
            });

            // emit event to pass data to parent
            this.$emit('visibility-setter', this.isVisible);
        },

        loading(value) {
            this.$parent.$emit('loading', value);
        }
    },

    methods: {
        /**
         * load objects using PaginatedContentMixin.getPaginatedObjects()
         *
         * @return {Promise} resp
         */
        async loadObjects() {
            this.loading = true;

            let response = this.getPaginatedObjects();

            response
                .then((objs) => {
                    this.loading = false;
                    return objs;
                })
                .catch((error) => {
                    // code 20 is user aborted fetch which is ok
                    if (error.code !== 20) {
                        this.loading = false;
                        console.error(error);
                    }
                });

            return response;
        },

        /**
         * send addedRelations to parent view
         *
         * @return {void}
         */
        appendRelations() {
            this.$emit('append-relations', this.pendingRelations);

            this.isVisible = false;
        },

        /**
         * handles ESC keyboard up event to hide current view
         *
         * @param {Event} event
         */
        handleKeyboard(event) {
            if (this.isVisible) {
                event.stopImmediatePropagation();
                event.preventDefault();
                this.hideRelationshipModal()
            }
        },

        /**
         * set component view's visibility to false
         * reset pendingRelations
         *
         * @return {void}
         */
        hideRelationshipModal() {
            this.pendingRelations = this.addedRelations;
            this.isVisible = false;
        },

        /**
         * helper function for template
         *
         * @return {Boolean} true if has at least a related object or a newly added object
         */
        hasElementsToShow() {
            const visible = this.objects.filter((obj) => {
                return !this.hideRelations.filter( (hidden) => obj.id === hidden.id).length;
            });
            return visible.length;
        },

        /**
         * helper function: convert array to string
         *
         * @param {Array} relations
         *
         * @return {String} string version of relations
         */
        relationFormatterHelper(relations) {
            let jsonString = '';
            try {
                jsonString = JSON.stringify(relations);
            } catch(err) {
                console.error(err);
            }
            return jsonString;
        },

        /**
         * helper function: check if array relations has element with id -> id
         *
         * @param {Array} relations
         * @param {Number} id
         *
         * @return {Boolean} true if id is in Array relations
         */
        containsId(relations, id) {
            return relations.filter((rel) => rel.id === id).length;
        },

    }

}
