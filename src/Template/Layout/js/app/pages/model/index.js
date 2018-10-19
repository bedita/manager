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
            // propertyTypes: [],
            savedPropertyTypes: [],
            newPropertyTypes: [],
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
            this.newPropertyTypes.splice(index, 1);
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
                addProperties: [...this.newPropertyTypes],
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
                    this.newPropertyTypes = [];
                    const props = json.map( (prop) => prop.data);
                    this.savedPropertyTypes.push(...props);
                }).catch((err) => {
                    console.log(err);
                });
        }
    }
};
