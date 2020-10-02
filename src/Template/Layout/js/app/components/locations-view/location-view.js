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
        apikey: String,
        apiurl: String,
        locationdata: Object,
    },

    data() {
        return {
            location: this.locationdata,
            fullAddress: false,
            coordinates: '',
        }
    },

    async mounted() {
        this.fullAddress = !!this.address(this.location);
    },

    methods: {
        onSubmitTitle(result) {
            this.$refs.address.value = this.address(result);
            this.location = result;
        },
        onSubmitAddress(result) {
            this.$refs.title.value = result.attributes.title;
            this.location = result;
            this.fullAddress = !!result;
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
                            const string = `${elem.attributes.address},${elem.attributes.postal_code},${elem.attributes.region}`;
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
            if (!model || !model.attributes) {
                return '';
            }

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

            string = string.replace(/<\/?[^>]+(>|$)/g, '');
            return string.substring(0, string.length - 2);
        },
        async geocode() {
            const retrieveGeocode = () => {
                const geocoder = new window.google.maps.Geocoder();
                geocoder.geocode({address: this.address(this.location)}, (results, status) => {
                    if (status === "OK" && results.length) {
                        const result = results[0];
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
