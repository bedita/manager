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
        this.createAutoCompleteConfig('title', (res) => res.attributes.title);
        this.createAutoCompleteConfig('address', (res) => res); // TODO Change
    },


    methods: {
        onChange() {
            this.$parent.$emit('location-changed');
        },
        onAddressInput() {
        },
        createAutoCompleteConfig(field, renderFunc) {
            const options = {
                baseClass: `autocomplete-${field}`,
                search: (input) => {
                    const requestUrl = `${BEDITA.base}/api/locations?filter[query]=${input}`;

                    return new Promise(resolve => {
                        if (input.length < 3) {
                            return resolve([]);
                        }

                        fetch(requestUrl, options)
                            .then(response => response.json())
                            .then(data => {
                                resolve(data.data);
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

                onSubmit: (result) => {
                    // do something,
                    console.log('cossa')
                    return true;
                },
            }

            return createAutocomplete(`.autocomplete-${field}`, options);
        },
    }
}
