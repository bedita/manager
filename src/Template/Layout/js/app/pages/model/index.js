/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Pages/Model/index.twig
 *  - Element/Toolbar/filter.twig
 *
 *
 * <model-index> component used for ModulesPage -> Index
 *
 */

import ModulesIndex from "app/pages/modules/index";

export default {
    extends: ModulesIndex,

    props: {
        csrfToken: {
            type: String,
            required: true,
        }
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            deletedPropertyTypes: [],
            savedPropertyTypes: [],
            newPropertyTypes: [],
            removePropertyTypes: [],
        };
    },

    /**
     * component methods
     */
    methods: {
        addRow() {
            this.newPropertyTypes.push({
                name: "",
                params: ""
            });
        },

        removeRow(element) {
            const index = this.newPropertyTypes.indexOf(element);
            if (index !== -1) {
                this.newPropertyTypes.splice(index, 1);
            }
        },

        removePropertyType(id) {
            this.removePropertyTypes.push(id);
        },

        undoRemovePropertyType(id) {
            const index = this.removePropertyTypes.indexOf(id);

            if (index !== -1) {
                this.removePropertyTypes.splice(index, 1);
            }
        },

        save() {
            let baseUrl = window.location.href;

            let headers = new Headers({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': this.csrfToken,
            });

            let payload = {
                _csrfToken: this.csrfToken,
                addPropertyTypes: [...this.newPropertyTypes],
                removePropertyTypes: [...this.removePropertyTypes],
            }

            const options = {
                method: 'POST',
                credentials: 'same-origin',
                headers,
                body: JSON.stringify( payload ),
            };

            const postUrl = `${baseUrl}/savePropertiesJson`;

            fetch(postUrl, options)
                .then((res) => res.json())
                .then((json) => {
                    const saved = json['saved'] || [];
                    const removed = json['removed'] || [];

                    const props = saved.filter(prop => prop).map(prop => {
                        const obj = prop.data;
                        if (obj.attributes && obj.attributes.params) {
                            obj.attributes.params = JSON.stringify(obj.attributes.params);
                        }
                        return obj;
                    });
                    this.savedPropertyTypes.push(...props);

                    // remove prop
                    this.deletedPropertyTypes.push(...removed);

                    // reset
                    this.newPropertyTypes = [];
                    this.removePropertyTypes = [];

                }).catch((err) => {
                    console.log(err);
                });
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
            return relations.filter((relId) => relId === id).length;
        },
    }
};
