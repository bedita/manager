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
            relationData: null,
            relationSchema: null,
            relationTypes: null,
            isRelationWithMedia: false,
        }
    },

    methods: {
        /**
         * retrieve json data for relation (this.relationName) and extrat params json schema
         *
         * @returns {Promise} promise with schema
         *
         */
        getRelationData() {
            let baseUrl = window.location.href;

            if (this.relationName) {
                // AppController endpoint for ajax request
                let requestUrl = `${baseUrl}/relationData/${this.relationName}`;

                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                };

                if (this.relationData) {
                    return Promise.resolve(this.relationData);
                }

                return fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        if (json.data && json.data.attributes) {
                            this.relationData = json.data.attributes;
                            this.relationSchema = this.getRelationSchema();
                            this.relationTypes = {
                                left: json.data.left,
                                right: json.data.right,
                            }

                            this.isRelationWithMedia = this.checkForMediaTypes(this.relationTypes.right);
                            return this.relationData;
                        }
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            } else {
                console.error('[RelationSchemaMixin] relationName not defined - can\'t load relation schema');
                return Promise.reject();
            }
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
                this.relationSchema = this.relationData !== null && this.relationData.params && this.relationData.params.properties;
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
