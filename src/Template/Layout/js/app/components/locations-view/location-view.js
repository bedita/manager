import { t } from 'ttag';

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
    template: `
        <div class="location mb-2 is-flex">
            <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
                <: index + 1 :>
            </div>
            <div class="location-form is-flex-column">
                <div class="is-flex">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Title') :>
                            <autocomplete
                                ref="title"
                                class="autocomplete-title"
                                :default-value="getDefaultTitle()"
                                :search="searchTitle"
                                base-class="autocomplete-title"
                                :get-result-value="getTitle"
                                @submit="onSubmitTitle"
                            >
                            </autocomplete>
                        </label>
                    </div>
                    <div class="is-flex-column is-expanded">
                        <label><: t('Address') :>
                            <autocomplete
                                ref="address"
                                class="autocomplete-address"
                                :default-value="getDefaultAddress()"
                                :search="searchAddress"
                                base-class="autocomplete-address"
                                :get-result-value="getAddress"
                                @submit="onSubmitAddress"
                                @change="onChangeAddress"
                            >
                            </autocomplete>
                        </label>
                    </div>
                </div>
                <div class="is-flex mt-1">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Long Lat Coordinates') :>
                            <div class="is-flex">
                                <input class="coordinates" type="text" :value="coordinates"/>
                                <button @click.prevent :disabled="!fullAddress" @click="geocode" class="get-coordinates icon-globe">
                                    <: t('GET') :>
                                </button>
                            </div>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Zoom
                            <input @change="onRelationDataChange" :value="zoom" data-name="zoom" type="number" min="2" max="20"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Pitch°
                            <input @change="onRelationDataChange" :value="pitch" data-name="pitch" type="number" min="0" max="60"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Bearing°
                            <input @change="onRelationDataChange" :value="bearing" data-name="bearing" type="number" min="-180" max="180"/>
                        </label>
                    </div>
                </div>
            </div>
        </div>`,
    props: {
        index: Number,
        apikey: String,
        apiurl: String,
        locationdata: Object,
    },

    data() {
        return {
            location: this.locationdata,
            fullAddress: false,
            coordinates: '',
            zoom: this.locationdata.meta.relation.params.zoom && parseInt(this.locationdata.meta.relation.params.zoom) || 2,
            pitch: this.locationdata.meta.relation.params.pitch && parseInt(this.locationdata.meta.relation.params.pitch) || 0,
            bearing: this.locationdata.meta.relation.params.bearing && parseInt(this.locationdata.meta.relation.params.bearing) || 0,
        }
    },

    async mounted() {
        this.fullAddress = !!this.address(this.location);
    },

    methods: {
        onRelationDataChange(event) {
            const target = event.target;
            const value = target.value;
            const attributeName = target.dataset['name'];
            this[attributeName] = parseInt(value);

            this.location.meta.relation.params.zoom = this.zoom;
            this.location.meta.relation.params.pitch = this.pitch;
            this.location.meta.relation.params.bearing = this.bearing;

            this.$parent.$emit('modified', this.location);
        },
        onSubmitTitle(result) {
            this.$refs.address.value = this.address(result);
            this.location = result; // set address on model from retrieved location
            this.$parent.$emit('modified', this.location);
        },
        onSubmitAddress(result) {
            this.$refs.title.value = result.attributes.title;
            this.location = result; // set address on model from retrieved location
            this.fullAddress = !!result;
            this.$parent.$emit('modified', this.location);
        },
        onChangeAddress(event) {
            const result = event.target.value;
            this.location.attributes.address = result;
            this.fullAddress = !!result;
            this.$parent.$emit('modified', this.location);
        },
        searchTitle(input) {
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

                        const filterFunc = (elem, input) => {
                            return elem.attributes.title && elem.attributes.title.toLowerCase().indexOf(input.toLowerCase()) !== -1;
                        };

                        // filter locations using the input 'filterFunc' function
                        results = results.filter((elem) => filterFunc(elem, input));
                        resolve(results);
                    });
            })
        },
        searchAddress(input) {
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

                        const filterFunc = (elem, input) => {
                            const string = this.address(elem);
                            return string && string.toLowerCase().indexOf(input.toLowerCase()) !== -1;
                        };

                        // filter locations using the input 'filterFunc' function
                        results = results.filter((elem) => filterFunc(elem, input));
                        resolve(results);
                    });
            })
        },
        getTitle(model) {
            return model.attributes && model.attributes.title;
        },
        getAddress(model) {
            return this.address(model);
        },
        getDefaultTitle() {
            return this.location && this.location.attributes && this.location.attributes.title;
        },
        getDefaultAddress() {
            return this.address(this.location);
        },
        address(model) {
            // TODO change according to the field that will be used for 'location' as full address
            if (!model || !model.attributes) {
                return '';
            }

            let string = '';
            if (model.attributes.address) {
                string = `${model.attributes.address}`;
            }

            return string.replace(/<\/?[^>]+(>|$)/g, '');
        },
        async geocode() {
            const retrieveGeocode = () => {
                const geocoder = new window.google.maps.Geocoder();
                geocoder.geocode({ address: this.address(this.location) }, (results, status) => {
                    if (status === "OK" && results.length) {
                        const result = results[0];

                        // Longitude, Latitude format: see https://docs.mapbox.com/api/#coordinate-format
                        this.coordinates = `${result.geometry.location.lng()}, ${result.geometry.location.lat()}`;
                    } else {
                        this.coordinates = '';
                        console.error("Error in geocoding address");
                    }
                });
            };

            if (document.querySelector('#googleapi')) {
                return retrieveGeocode();
            }

            // init script
            var script = document.createElement('script');
            script.src = `${this.apiurl}js?key=${this.apikey}&callback=initMap`;
            script.defer = true;
            script.id = "googleapi";

            window.initMap = () => {
               retrieveGeocode();
            };
            document.head.appendChild(script);
        },
    }
}
