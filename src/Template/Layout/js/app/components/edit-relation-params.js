/**
 * View component used for editing relations param
 *
 */


export default {
    // injected methods provided by Main App
    inject: ['returnDataFromPanel','closePanel'],

    props: {
        relation: {
            type: Object,
            default: () => {},
        },
    },

    computed: {
        relatedStatus() {
            return this.relation.related.attributes.status;
        },
        relatedType() {
            let type = '(not available)';
            if (this.relation && this.relation.related) {
                type = this.relation.related.type;
            }

            return type;
        },
        relatedName() {
            let name = '(empty)';
            if (this.relation && this.relation.related) {
                name = this.relation.related.attributes.title;
            }

            return name;
        }
    },

    data() {
        return {
            oldParams: {},
            editingParams: {},
            priority: null,
            isModified: false,
        }
    },

    watch: {
        relation(value) {
            if (value) {
                Object.assign(this.oldParams, value.related.meta.relation.params);
                Object.assign(this.editingParams, value.related.meta.relation.params);

                this.priority = value.related.meta.relation.priority;
            } else {
                this.oldParams = {};
                this.editingParams = {};
                this.isModified = false;
                this.priority = null;
            }
        },
    },

    methods: {
        saveParams() {
            if (Object.keys(this.editingParams).length) {
                this.relation.related.meta.relation.params = this.editingParams;
            } else {
                delete this.relation.related.meta.relation.params;
            }
            this.relation.related.meta.relation.priority = this.priority;
            this.closeParamsView();
            this.returnDataFromPanel({
                action: 'edit-params',
                item: this.relation,
            });
        },

        closeParamsView() {
            this.editingParams = {};
            this.closePanel();
        },

        checkParams() {
            this.isModified = !!Object.keys(this.editingParams).filter((index) => {
                return this.editingParams[index] !== '' && this.editingParams[index] !== this.oldParams[index];
            }).length || this.relation.related.meta.relation.priority !== this.priority;
        },
    },
}
