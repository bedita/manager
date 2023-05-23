import { t } from 'ttag';

const options = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

function convertFromPoint(input) {
    if (!input) {
        return;
    }
    let match = input.match(/point\(([^)]*)\)/i);
    if (!match) {
        return;
    }
    return match[1].split(' ').join(', ');
}

function convertToPoint(input) {
    if (!input) {
        return;
    }
    if (input.match(/point\(([^)]*)\)/i)) {
        return input;
    }

    let [lon, lat] = input.split(/\s*,\s*/);
    return `POINT(${lon} ${lat})`;
}

function stripHtml(str) {
    return str.replace(/<\/?[^>]+(>|$)/g, '');
}

/**
 * <location-view> component used for ModulesPage -> View
 *
 * Handle Locations and reverse geocoding from addresses
 */
export default {
    template: `<div class="location mb-2 is-flex">
        <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
            <: index + 1 :>
        </div>
        <div class="location-form is-flex-column">
            <div class="is-flex">
                <div class="is-flex-column is-expanded">
                    <label>
                        ${t`Title`}
                        <autocomplete
                            autocomplete="none"
                            ref="title"
                            class="autocomplete-title"
                            :default-value="title"
                            :search="searchTitle"
                            base-class="autocomplete-title"
                            :get-result-value="getResultTitle"
                            @submit="onAutocompleteSubmit"
                            @input="onInputTitle"
                            @change="onChange"
                            :debounce-time="500"
                            :disabled="!!id"
                        >
                        </autocomplete>
                    </label>
                </div>
                <div class="is-flex-column is-expanded">
                    <label>
                        ${t`Address`}
                        <autocomplete
                            autocomplete="none"
                            ref="address"
                            class="autocomplete autocomplete-address"
                            :default-value="address"
                            :search="searchAddress"
                            base-class="autocomplete-address"
                            :get-result-value="getResultAddress"
                            @submit="onAutocompleteSubmit"
                            @input="onInputAddress"
                            @change="onChange"
                            :debounce-time="500"
                            :disabled="!!id"
                        >
                        </autocomplete>
                    </label>
                </div>
            </div>
            <div class="is-flex mt-1">
                <div class="is-flex-column is-expanded">
                    <label>
                        ${t`Long Lat Coordinates`}
                        <div class="is-flex">
                            <input class="coordinates" type="text" v-model="coordinates" @change="onChange" :disabled="!!id" />
                            <button class="get-coordinates" @click.prevent="geocode" :disabled="!apiKey || !address">
                                <Icon icon="carbon:wikis"></Icon>
                                <span class="ml-05">${t`GET`}</span>
                            </button>
                        </div>
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        Zoom
                        <input @change="onChange" v-model.number="zoom" type="number" min="2" max="20" :disabled="!!id" />
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        Pitch°
                        <input @change="onChange" v-model.number="pitch" type="number" min="0" max="60" :disabled="!!id" />
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        Bearing°
                        <input @change="onChange" v-model.number="bearing" type="number" min="-180" max="180" :disabled="!!id" />
                    </label>
                </div>
            </div>
            <div class="location-buttons">
                <a v-if="id" class="button button-text-white" :href="$helpers.buildViewUrl(id)" target="_blank">
                    <Icon icon="carbon:launch"></Icon>
                    <span class="ml-05">${t`edit`}</span>
                </a>
                <button @click.prevent="onRemove" class="button button-text-white remove">
                <Icon icon="carbon:unlink"></Icon>
                    <span class="ml-05">${t`remove`}</span>
                </button>
            </div>
        </div>
    </div>`,

    props: {
        index: Number,
        apiKey: String,
        apiUrl: String,
        locationData: Object,
        relationName: String,
        relationLabel: String,
    },

    data() {
        return {
            location: this.locationData,
            id: this.locationData.id,
            title: this.locationData.attributes.title,
            address: this.locationData.attributes.address,
            coordinates: convertFromPoint(this.locationData.attributes.coords),
            zoom: parseInt(this.locationData.meta &&
                this.locationData.meta.relation &&
                this.locationData.meta.relation.params &&
                this.locationData.meta.relation.params.zoom) || 2,
            pitch: parseInt(this.locationData.meta &&
                this.locationData.meta.relation &&
                this.locationData.meta.relation.params &&
                this.locationData.meta.relation.params.pitch) || 0,
            bearing: parseInt(this.locationData.meta &&
                this.locationData.meta.relation &&
                this.locationData.meta.relation.params &&
                this.locationData.meta.relation.params.bearing) || 0,
        }
    },

    methods: {
        onChange() {
            this.location.attributes.title = this.title;
            this.location.attributes.address = this.address;
            this.location.attributes.status = 'on';
            this.location.attributes.coords = convertToPoint(this.coordinates);
            this.location.meta.relation = this.location.meta.relation || { params: {} }; // ensure relation object
            this.location.meta.relation.params.zoom = `${this.zoom}`;
            this.location.meta.relation.params.pitch = `${this.pitch}`;
            this.location.meta.relation.params.bearing = `${this.bearing}`;

            this.$parent.$emit('updated', this.index, this.location);
        },
        onAutocompleteSubmit(result) {
            if (!result) {
                return;
            }

            // use original fetched location instead of the "potentially" edited one (see `searchAddress` method)
            const location = this.fetchedLocations.find((item) => item.id == result.id);
            if (!location) {
                return;
            }

            this.location = location;
            this.title = this.location.attributes.title;
            this.address = this.location.attributes.address;
            this.coordinates = convertFromPoint(this.location.attributes.coords);
            this.onChange();
        },
        onInputTitle(event) {
            this.title = event.target.value;
        },
        onInputAddress(event) {
            this.address = this.cleanAddress(event.target.value);
        },
        onRemove() {
            this.$parent.$emit('removed', this.index);
        },
        async searchTitle(input) {
            const results = [];
            const collected = [];

            // do not search with less than 3 chars
            if (input.length >= 3) {
                let page = 1;
                while (results.length < 20) {
                    const url = new URL('/api/locations', BEDITA.base);
                    url.searchParams.set('filter[query]', input.trim());
                    url.searchParams.set('sort', 'title');
                    url.searchParams.set('page_size', '100');
                    url.searchParams.set('page', page++);

                    const response = await fetch(url, options);
                    const json = await response.json();
                    const titles = json.data.reduce((acc, location) => {
                        const title = location && location.attributes && location.attributes.title && location.attributes.title.trim().toLowerCase();
                        if (!title || acc[title]) {
                            return acc;
                        }
                        if (collected.includes(title)) {
                            // avoid duplicates
                            return acc;
                        }
                        if (title.indexOf(input.toLowerCase()) === -1) {
                            // only pick locations that include input string in the address
                            return acc;
                        }
                        acc[title] = location;
                        return acc;
                    }, {});

                    collected.push(...Object.keys(titles));
                    results.push(...Object.values(titles));

                    if (json.meta.pagination.page_count < page) {
                        break;
                    }
                }
            }

            this.fetchedLocations = results.slice();

            return results;
        },
        async searchAddress(input) {
            const results = [];
            const collected = [];

            // do not search with less than 3 chars
            if (input.length >= 3) {
                let page = 1;
                while (results.length < 20) {
                    const url = new URL('/api/locations', BEDITA.base);
                    url.searchParams.set('filter[query]', input.trim());
                    url.searchParams.set('sort', 'address');
                    url.searchParams.set('page_size', '100');
                    url.searchParams.set('page', page++);

                    const response = await fetch(url, options);
                    const json = await response.json();
                    const addresses = json.data.reduce((acc, location) => {
                        const address = location && location.attributes && this.cleanAddress(location.attributes.address).toLowerCase();
                        if (!address || acc[address]) {
                            return acc;
                        }
                        if (collected.includes(address)) {
                            // avoid duplicates
                            return acc;
                        }
                        if (address.indexOf(input.toLowerCase()) === -1) {
                            // only pick locations that include input string in the address
                            return acc;
                        }
                        acc[address] = location;
                        return acc;
                    }, {});

                    collected.push(...Object.keys(addresses));
                    results.push(...Object.values(addresses));

                    if (json.meta.pagination.page_count < page) {
                        break;
                    }
                }
            }

            this.fetchedLocations = results.slice();

            return results;
        },
        /**
         * Title to show in the autocomplete component.
         * @param {Object} model Autocomplete result object
         * @returns {String}
         */
        getResultTitle(model) {
            return (model && model.attributes && model.attributes.title) || '';
        },
        /**
         * Address to show in the autocomplete component.
         * @param {Object} model Autocomplete result object
         * @returns {String}
         */
        getResultAddress(model) {
            return (model && model.attributes && this.cleanAddress(model.attributes.address)) || '';
        },
        cleanAddress(address) {
            if (!address) {
                return '';
            }

            return stripHtml(`${address.trim()}`);
        },
        async geocode() {
            const retrieveGeocode = () => {
                if (!window.google || !window.google.maps) {
                    return;
                }

                const geocoder = new window.google.maps.Geocoder();
                geocoder.geocode({ address: this.address }, (results, status) => {
                    if (status === 'OK' && results.length) {
                        const result = results[0];

                        // Longitude, Latitude format: see https://docs.mapbox.com/api/#coordinate-format
                        this.coordinates = `${result.geometry.location.lng()}, ${result.geometry.location.lat()}`;
                    } else {
                        this.coordinates = '';
                        console.error('Error in geocoding address');
                    }
                    this.onChange();
                });
            };

            if (document.querySelector('#googleapi')) {
                return retrieveGeocode();
            }

            // init script
            var script = document.createElement('script');
            script.src = `${this.apiUrl}js?key=${this.apiKey}&callback=initMap`;
            script.defer = true;
            script.id = 'googleapi';

            window.initMap = () => {
                retrieveGeocode();
            };
            document.head.appendChild(script);
        },
    }
}
