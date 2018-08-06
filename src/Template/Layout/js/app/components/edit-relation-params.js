/**
 *
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
            editingParams: {},
        }
    },

    watch: {
        relation(value) {
            if (value) {
                Object.assign(this.editingParams, value.related.meta.relation.params);
            } else {
                this.editingParams = {};
            }
        },
    },

    methods: {
        saveParams() {
            this.relation.related.meta.relation.params = this.editingParams;
            this.closeParamsView();
            this.returnDataFromPanel({
                action: 'edit-params',
                item: this.relation,
            });
        },

        closeParamsView() {
            this.editingParams = {};
            this.closePanel();
        }
    }
}
