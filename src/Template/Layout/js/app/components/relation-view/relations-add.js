/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relations-add> component used for Panel
 *
 */

import { PaginatedContentMixin } from 'app/mixins/paginated-content';
import decamelize from 'decamelize';
import sleep from 'sleep-promise';

export default {
    inject: ['returnDataFromPanel', 'closePanel'],      // injected methods provided by Main App

    mixins: [ PaginatedContentMixin ],
    props: {
        relationName: {
            type: String,
            default: '',
        },
        alreadyInView: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            method: 'relationshipsJson',
            endpoint: '',
            selectedObjects: [],
        }
    },

    watch: {
        relationName: {
            immediate: true,
            handler(newVal, oldVal) {
                if(newVal) {
                    this.selectedObjects = [];
                    this.endpoint = `${this.method}/${newVal}`;
                    this.loadObjects();
                }
                // clear objects when relationName is empty (panel closed)
                if (newVal === '') {
                    sleep(500).then(() => this.objects = []);
                }
            },
        },
    },

    methods: {
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

}
