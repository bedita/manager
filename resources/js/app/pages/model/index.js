/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Pages/Model/index.twig
 *
 * <model-index> component used for ModulesPage -> Index
 *
 * @extends ModulesIndex
 */

import ModulesIndex from 'app/pages/modules/index';
import { confirm, error as showError} from 'app/components/dialog/dialog';
import { Icon } from '@iconify/vue2';
import { t } from 'ttag';

export default {
    extends: ModulesIndex,

    components: {
        AutosizeTextarea: () => import(/* webpackChunkName: "autosize-textarea" */'app/components/autosize-textarea'),
        TagForm: () => import(/* webpackChunkName: "tag-form" */'app/components/tag-form/tag-form'),
        Icon,
    },

    props: {
        resources: {},  // loaded resources from template
        csrfToken: {
            type: String,
            required: true,
        },   // csfrToken used for api call
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            propertyTypes: [],  // loaded property_type objects
            deletedPropertyTypes: [],   // deleted property_type objects to be hidden
            savedPropertyTypes: [], // saved property_type objects to show
            newPropertyTypes: [],   // stage property_type objects TO ADD
            removePropertyTypes: [],    // stage property_type objects to REMOVE
            editPropertyTypes: [],  // stage property_type objects to EDIT
            dialog: null,
        };
    },

    mounted() {
        // parse resource list received from template
        this.propertyTypes = JSON.parse(this.resources);
    },

    computed: {
        /**
         * check if page has been modified, if so emits a change event
         *
         * @emits Event#change thrown when page has been modified
         *
         * @returns
         */
        formIsModified() {
            const isChanged = !!(this.newPropertyTypes.length || this.removePropertyTypes.length || this.editPropertyTypes.length);

            if (this.$el) {
                this.$el.dispatchEvent(new CustomEvent('change', {
                    bubbles: true,
                    detail: {
                        id: this.$vnode.tag,
                        isChanged,
                    },
                }));
            }
            return isChanged;
        }
    },

    /**
     * component methods
     */
    methods: {
        /**
         * edit property type params
         *
         * @param {Number} id resource id
         * @param {String} value params value
         *
         * @returns {void}
         */
        editPropParams(id, value) {
            let params = null;
            try {
                if (value !== "") {
                    params = JSON.parse(value);
                    if (!Object.keys(params).length) {
                        params = null;
                    }
                }
            } catch (error) {
                console.info('[editPropParams] params JSON parse failed - skipping this one...');
                return;
            }

            const editedType = {
                id,
                attributes: {
                    params: params,
                },
            }

            this.editPropertyType(editedType);
        },

        /**
        * edit property type name
        *
        * @param {Number} id resource id
        * @param {String} value name value
        *
        * @returns {void}
        */
        editPropName(id, value) {
            const editedType = {
                id,
                attributes: {
                    name: value,
                },
            }

            this.editPropertyType(editedType);
        },

        /**
         * add new empty object in newPropertyTypes array
         *
         * @returns {void}
         */
        addRow() {
            this.newPropertyTypes.push({
                name: "",
                params: ""
            });
        },

        /**
        * remove specific element from newPropertyTypes array
        *
        * @returns {void}
        */
        removeRow(element) {
            const index = this.newPropertyTypes.indexOf(element);
            if (index !== -1) {
                this.newPropertyTypes.splice(index, 1);
            }
        },

        /**
        * add specific element with {id} to removePropertyTypes array
        *
        * @param {Number} id id of object to remove
        *
        * @returns {void}
        */
        removePropertyType(id) {
            this.removePropertyTypes.push(id);
        },

        /**
         * unstage element from removePropertyTypes array
         *
         * @param {Number} id id to revert
         */
        undoRemovePropertyType(id) {
            const index = this.removePropertyTypes.indexOf(id);

            if (index !== -1) {
                this.removePropertyTypes.splice(index, 1);
            }
        },

        /**
         * save resources (add/edit/delete) using controller's method savePropertiesJson
         *
         * @returns {void}
         */
        save() {
            if (!this.removePropertyTypes.length) {
                return this.performSave();
            }

            // some property type has been removed. ask for confirmation before proceeding.
            let types = this.removePropertyTypes.map((removed) => this.propertyTypes.find((type) => type.id == removed).attributes.name).join(', ');
            this.dialog = confirm(
                t`Do you really want to remove these property types? ${types}`,
                t`yes, proceed`,
                () => this.performSave()
            );
        },

        performSave() {
            const headers = new Headers({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': BEDITA.csrfToken,
            });

            const payload = {
                addPropertyTypes: [...this.newPropertyTypes],
                removePropertyTypes: [...this.removePropertyTypes],
                editPropertyTypes: [...this.editPropertyTypes],
            }

            const options = {
                method: 'POST',
                credentials: 'same-origin',
                headers,
                body: JSON.stringify( payload ),
            };

            const postUrl = `${BEDITA.base}/model/property_types/save`;

            fetch(postUrl, options)
                .then((res) => res.json())
                .then((json) => {
                    const saved = json?.['saved'] || [];
                    const removed = json?.['removed'] || [];
                    const edited = json?.['edited'] || [];
                    const err = json?.['error'] || null;
                    if (err) {
                        showError(err);
                        return;
                    }

                    // store saved property types
                    this.savedPropertyTypes.push(...saved);

                    // store removed property types
                    this.deletedPropertyTypes.push(...removed);

                    edited.forEach((entry) => {
                        // update propertyTypes list
                        this.propertyTypes = this.propertyTypes.map((propertyType) => {
                            if (propertyType.id === entry.id) {
                                return entry;
                            }
                            return propertyType;
                        });

                        this.savedPropertyTypes = this.savedPropertyTypes.map((propertyType) => {
                            // update savedPropertyTypes list
                            if (propertyType.id === entry.id) {
                                return entry;
                            }
                            return propertyType;
                        });
                    });

                    // clean up wrong jsons an restore original value
                    this.$children.forEach((component) => {
                        try {
                            JSON.parse(component.text);
                        } catch (error) {
                            // if new value is not valid, restore the previous one
                            component.text = component.originalValue;
                        }
                    });

                    // reset
                    this.newPropertyTypes = [];
                    this.removePropertyTypes = [];
                    this.editPropertyTypes = [];
                    this.dialog?.hide();
                }).catch((err) => {
                    console.log(err);
                    this.dialog?.hide();
                });
        },

        /**
         * setup edited element
         *
         * @param {Object} type edited propertyType element
         *
         * @returns {void}
         */
        editPropertyType(type) {
            // concat propertyTypes and savePropertyTypes
            let allPropertyTypes = [ ...this.propertyTypes, ...this.savedPropertyTypes];

            // find old value for current element
            const [original] = allPropertyTypes.filter((propertyType) => propertyType.id == type.id);

            // check if element already edited
            const exists = this.editPropertyTypes.filter((propertyType) => propertyType.id === type.id).length;
            if (exists) {
                // ..modify edited element
                this.editPropertyTypes = this.editPropertyTypes.map((propertyType) => {
                    if (propertyType.id === type.id) {
                        return {
                            id: type.id,
                            attributes: {...propertyType.attributes, ...type.attributes}
                        };
                    }
                    return propertyType;
                });
            } else {
                // add edited element
                this.editPropertyTypes.push(type);
            }

            // prune edited element that are equal to original value
            this.editPropertyTypes = this.editPropertyTypes.filter((propertyType) => {
                const equals = Object.keys(propertyType.attributes).filter(
                    (attributeName) => {
                        // very simple equivalency check
                        return JSON.stringify(propertyType.attributes[attributeName]) === JSON.stringify(original.attributes[attributeName]);
                    }
                ).length === Object.keys(propertyType.attributes).length;

                return !equals;
            });
        },

        /**
        * helper function: correctly format property params
        *
        * @param {Array} object array of property_types
        *
        * @return {void}
        */
        formatParams(object) {
            let formatted = '';

            if (object && object.attributes && object.attributes.params) {
                try {
                    formatted = JSON.stringify(object.attributes.params, null, 4);
                } catch (error) {
                    formatted = error.message;
                }
            }
            return formatted;
        },

        /**
        * helper function: check if array relations has element with id -> id
        *
        * @param {Array} relations array of relations
        * @param {Number} id id to compare
        *
        * @return {Boolean} true if id is in Array relations
        */
        containsId(array, id) {
            return array.filter((element) => {
                if (typeof element === 'object' && 'id' in element) {
                    return element.id === id;
                }
                return element === id
            }).length;
        },
    }
};
