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
                            class="autocomplete-address"
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
                            <button class="get-coordinates icon-globe" @click.prevent="geocode" :disabled="!apiKey || !address">
                                ${t`GET`}
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
                <a v-if="id" class="button button-text-white icon-edit" :href="$helpers.buildViewUrl(id)" target="_blank">${t`edit`}</a>
                <button @click.prevent="onRemove" class="button button-text-white icon-unlink remove">${t`remove`}</button>
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
        searchTitle(input) {
            const requestUrl = `${BEDITA.base}/api/locations?filter[query]=${input}&sort=title`;

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

                        // only pick locations that include input string in the title
                        results = results.filter((location) => location.attributes.title && location.attributes.title.toLowerCase().indexOf(input.toLowerCase()) !== -1);
                        // store raw filtered data
                        this.fetchedLocations = results.slice();

                        resolve(results);
                    });
            })
        },
        searchAddress(input) {
            const requestUrl = `${BEDITA.base}/api/locations?filter[query]=${input}&sort=address`;

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

                        // only pick locations that include input string in the address
                        results = results.filter((location) => {
                            const address = location && location.attributes && this.cleanAddress(location.attributes.address);
                            return address && address.toLowerCase().indexOf(input.toLowerCase()) !== -1;
                        });
                        // store raw fetched data
                        this.fetchedLocations = results.slice();

                        resolve(results);
                    });
            });
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

            return stripHtml(`${address}`);
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
