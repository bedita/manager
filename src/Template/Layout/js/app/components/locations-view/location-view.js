import { t } from 'ttag';
import Autocomplete from '@trevoreyre/autocomplete-vue';
import Vue from 'vue';

// for compatibility with the third-party Vue component
const AutocompleteConstructor = Vue.extend(Autocomplete);
function createAutocomplete(el, options) {
	let component = new AutocompleteConstructor({
		propsData: options
	});
	component.$mount(el);
	return component;
}

const options = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <location-view> component used for ModulesPage -> View
 *
 * Handle Locations and reverse geocoding from addresses
 *
 *
 */
export default {
    components: {
        Autocomplete,
    },
    template: `
        <div class="location mb-2 is-flex">
            <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
                <: index + 1 :>
            </div>
            <div class="location-form is-flex-column">
                <div class="is-flex">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Title') :>
                            <div class="autocomplete-title">
                                <input type="text" class="autocomplete-input">
                                <ul class="autocomplete-result-list"></ul>
                            </div>
                        </label>
                    </div>
                    <div class="is-flex-column is-expanded">
                        <label><: t('Address') :>
                            <div class="autocomplete-address">
                                <input type="text" class="autocomplete-input"">
                                <ul class="autocomplete-result-list"></ul>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="is-flex mt-1">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Long Lat Coordinates') :>
                            <div class="is-flex">
                                <input class="coordinates" type="text"/>
                                <button disabled class="get-coordinates icon-globe">
                                    <: t('GET') :>
                                </button>
                            </div>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Zoom
                            <input type="number" min="2" max="20"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Pitch°
                            <input type="number" min="0" max="60"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Bearing°
                            <input type="number" min="-180" max="180"/>
                        </label>
                    </div>
                </div>
            </div>
        </div>`,

    props: {
        index: Number,
        location: Object,
    },

    data() {
        return {
        }
    },

    async mounted() {
        // create two autocomplete fields with configurations
        this.createAutoCompleteConfig(
            'title',
            (res) => res.attributes.title,
            (elem, input) => {
                return elem.attributes.title && elem.attributes.title.toLowerCase().indexOf(input) !== -1;
            },
            this.location && this.location.attributes.title || '',
        );

        const getAddress = (model) => {
            if (!model.attributes) {
                return '';
            }
            ù
            let string = '';
            if (model.attributes.address) {
                string += `${model.attributes.address}, `;
            }

            if (model.attributes.postal_code) {
                string += `${model.attributes.postal_code}, `;
            }

            if (model.attributes.region) {
                string += `${model.attributes.region}, `;
            }

            return string.substring(0, string.length - 2);
        };

        this.createAutoCompleteConfig(
            'address',
            (res) => {
                return getAddress(res);
            },
            (elem, input) => {
                const string = `${elem.attributes.address},${elem.attributes.postal_code},${elem.attributes.region}`;
                return string && string.toLowerCase().indexOf(input) !== -1;
            },
            getAddress(this.location)
        );
    },


    methods: {
        createAutoCompleteConfig(field, renderFunc, filterFunc, defaultValue) {
            const options = {
                baseClass: `autocomplete-${field}`,
                defaultValue,
                search: (input) => {
                    const requestUrl = `${BEDITA.base}/api/locations?filter[query]=${input}`;

                    return new Promise(resolve => {
                        if (input.length < 3) {
                            // do not search with less than 3 chars
                            return resolve([]);
                        }

                        fetch(requestUrl, options)
                            .then(response => response.json())
                            .then(data => {
                                let results = data.data;

                                if (!results) {
                                    return resolve([]);
                                }

                                // filter locations using the input 'filterFunc' function
                                results = results.filter((elem) => filterFunc(elem, input));
                                resolve(results);
                            });
                    })
                },

                getResultValue: (result) => {
                    return renderFunc(result);
                },

                renderResult: (result) => {
                    return `
                        <li>
                            <div class="location-title">
                                ${result.attributes.title}
                            </div>
                        </li>
                        `
                },

                // TODO sistemare il submit
            }

            return createAutocomplete(`.autocomplete-${field}`, options);
        },
    }
}
