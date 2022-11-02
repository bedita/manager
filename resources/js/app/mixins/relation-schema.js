/**
 * Mixins: RelationSchemaMixin
 *
 * @prop {Boolean} tabOpen determines whether the property content is visible or not
 * @prop {String} label label of the property view
 * @
 *
 */

import { ACCEPTABLE_MEDIA } from 'config/config';

export const RelationSchemaMixin = {
    data() {
        return {
            relationSchema: null,
            relationTypes: null,
            isRelationWithMedia: false,
        }
    },

    mounted() {
        this.parseRelationData();
    },

    methods: {
        /**
         * retrieve json data for relation (this.relationName) and extrat params json schema
         *
         * @returns {Promise} promise with schema
         *
         */
        parseRelationData() {
            if (!this.relationData) {
                return [];
            }

            this.relationSchema = this.getRelationSchema();
            this.relationTypes = {
                left: this.relationData.left,
                right: this.relationData.right,
            }
            if (this.relationData.attributes.name === 'children') {
                this.relationTypes.right = this.relationTypes.right.filter((type) => type !== 'folders');
            }

            this.isRelationWithMedia = this.checkForMediaTypes(this.relationTypes.right);
            return this.relationData;
        },

        /**
         * check if passed array contains media types
         *
         * @param {Array} relatedTypes
         */
        checkForMediaTypes(relatedTypes) {
            const mediaChecker = (acc, entry) => acc = acc || ACCEPTABLE_MEDIA.indexOf(entry) !== -1;
            return relatedTypes && relatedTypes.reduce(mediaChecker, false) || false;
        },

        /**
         * helper function to check if relation schema is loaded and relations has params
         *
         * @returns {Boolean} true if relation has params
         *
         */
        relationHasParams() {
            return this.relationData !== null && !!this.getRelationSchema();
        },

        /**
         * extract params json schema from relationData
         *
         * @returns {Object} params json schema
         *
         */
        getRelationSchema() {
            if (this.relationSchema === null) {
                this.relationSchema = this.relationData !== null && this.relationData.attributes.params && this.relationData.attributes.params.properties;
            }
            return this.relationSchema;
        },

        /**
         * helper function to get params value from an object according to bedita api convention
         *
         * @param {Object} object
         * @param {String} paramKey
         *
         * @returns {Boolean} true if relation has params
         *
         */
        getParamHelper(object, paramKey) {
            return object.meta.relation.params && object.meta.relation.params[paramKey] || null;
        },
    }
}
