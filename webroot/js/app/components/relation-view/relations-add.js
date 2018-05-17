/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <add-relations-view> component used for Panel
 *
 */

Vue.component('relations-add', {
    mixins: [ PaginatedContentMixin ],
    props: ['relationName', 'alreadyInView'],
    data() {
        return {
            method: 'relationshipsJson',
            endpoint: '',
            selectedObjects: [],
        }
    },

    computed: {
        relationHumanizedName() {
            return humanize(this.relationName);
        }
    },

    watch: {
        relationName: {
            immediate: true,
            handler(newVal, oldVal) {
                this.selectedObjects = [];
                this.endpoint = `${this.method}/${newVal}`;
                this.loadObjects();
            },
        }
    },

    methods: {
        returnData() {
            var data = {
                objects: this.selectedObjects,
                relationName: this.relationName,
            };
            this.$root.onRequestPanelToggle({ returnData: data });
        },
        toggle(object, evt) {
            let position = this.selectedObjects.indexOf(object);
            if(position != -1) {
                this.selectedObjects.splice(position, 1);
            } else {
                this.selectedObjects.push(object);
            }
        },
        isAlreadyRelated() {
            return true;
        },
        // form mixin
        async loadObjects() {
            this.loading = true;
            let resp = await this.getPaginatedObjects();
            this.loading = false;
            return resp;
        },
    }

});
