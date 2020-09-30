import { t } from 'ttag';
import Autocomplete from '@trevoreyre/autocomplete-vue';
import Vue from 'vue';

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
                                <input @change="onChange" type="text" class="autocomplete-input">
                                <ul class="autocomplete-result-list"></ul>
                            </div>
                        </label>
                    </div>
                    <div class="is-flex-column is-expanded">
                        <label><: t('Address') :>
                            <div class="autocomplete-address">
                                <input @change="onChange" type="text" class="autocomplete-input">
                                <ul class="autocomplete-result-list"></ul>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="is-flex mt-1">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Long Lat Coordinates') :>
                            <div class="is-flex">
                                <input class="coordinates" @change="onChange" type="text"/>
                                <button disabled class="get-coordinates icon-globe">
                                    <: t('GET') :>
                                </button>
                            </div>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Zoom
                            <input @change="onChange" type="number" min="2" max="20"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Pitch°
                            <input @change="onChange" type="number" min="0" max="60"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Bearing°
                            <input @change="onChange" type="number" min="-180" max="180"/>
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
        this.createAutoCompleteConfig('title', (res) => res.attributes.title, (elem, input) => elem.attributes.title.indexOf(input) !== -1);
        this.createAutoCompleteConfig('address',
        (res) => {
            let string = '';
            if (res.attributes.address) {
                string += `${res.attributes.address}, `;
            }

            if (res.attributes.postal_code) {
                string += `${res.attributes.postal_code}, `;
            }

            if (res.attributes.region) {
                string += `${res.attributes.region}, `;
            }

            return string.substring(0, string.length - 2);
        },
        (elem, input) => {
            const string = `${elem.attributes.address},${elem.attributes.postal_code},${elem.attributes.region}`;
            return string.indexOf(input) !== -1;
        });
    },


    methods: {
        onChange() {
            this.$parent.$emit('location-changed');
        },
        onAddressInput() {
        },
        createAutoCompleteConfig(field, renderFunc, filterFunc) {
            const options = {
                baseClass: `autocomplete-${field}`,
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

                                // filter locations
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
            }

            return createAutocomplete(`.autocomplete-${field}`, options);
        },
    }
}
