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
    let [lon, lat] = match[1].split(' ');
    return `${lat}, ${lon}`;
}

function convertToPoint(input) {
    if (!input) {
        return;
    }
    let [lat, lon] = input.split(/\s*,\s*/);
    return `POINT(${lon} ${lat})`;
}

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
            <input type="hidden" name="relations[has_location][replaceRelated][]" :value="locationValue" />
            <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
                <: index + 1 :>
            </div>
            <div class="location-form is-flex-column">
                <div class="is-flex">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Title') :>
                            <autocomplete
                                autocomplete="none"
                                ref="title"
                                class="autocomplete-title"
                                :default-value="getDefaultTitle()"
                                :search="searchTitle"
                                base-class="autocomplete-title"
                                :get-result-value="getTitle"
                                @submit="onSubmitTitle"
                                @change="onChangeTitle"
                            >
                            </autocomplete>
                        </label>
                    </div>
                    <div class="is-flex-column is-expanded">
                        <label><: t('Address') :>
                            <autocomplete
                                autocomplete="none"
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
                <div class="location-buttons">
                    <button @click.prevent @click="onRemove" class="icon-unlink remove"> <: t("remove") :> </button>
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
            title: this.locationdata.attributes.title,
            fullAddress: this.address(this.locationdata),
            coordinates: convertFromPoint(this.locationdata.attributes.coords),
            zoom: this.locationdata.meta.relation.params.zoom && parseInt(this.locationdata.meta.relation.params.zoom) || 2,
            pitch: this.locationdata.meta.relation.params.pitch && parseInt(this.locationdata.meta.relation.params.pitch) || 0,
            bearing: this.locationdata.meta.relation.params.bearing && parseInt(this.locationdata.meta.relation.params.bearing) || 0,
        }
    },

    computed: {
        locationValue() {
            return JSON.stringify({
                id: this.location && this.location.id,
                type: 'locations',
                attributes: {
                    status: 'on',
                    ...(this.location && Object.keys(this.location.attributes).length && this.location.attributes || {
                        title: this.title || '',
                        address: this.fullAddress || '',
                    }),
                    coords: convertToPoint(this.coordinates),
                },
                meta: {
                    relation: {
                        params: {
                            zoom: `${this.zoom}`,
                            pitch: `${this.pitch}`,
                            bearing: `${this.bearing}`,
                        },
                    },
                },
            });
        },
    },

    methods: {
        onRemove() {
            this.$parent.$emit('removed', this.index);
        },
        onRelationDataChange(event) {
            const target = event.target;
            const value = target.value;
            const attributeName = target.dataset['name'];
            this[attributeName] = parseInt(value);

            this.location.meta.relation.params.zoom = `${this.zoom}`;
            this.location.meta.relation.params.pitch = `${this.pitch}`;
            this.location.meta.relation.params.bearing = `${this.bearing}`;
        },
        onSubmitTitle(result) {
            this.$refs.address.value = this.address(result);
            this.location = result; // set address on model from retrieved location
            this.coordinates = convertFromPoint(this.location.attributes.coords);
        },
        onSubmitAddress(result) {
            this.$refs.title.value = result.attributes.title;
            this.fullAddress = !!result;
            this.location = result; // set address on model from retrieved location
            this.coordinates = convertFromPoint(this.location.attributes.coords);
        },
        onChangeTitle(event) {
            const title = event.target.value;
            this.title = title;
            this.location.attributes.title = title;
        },
        onChangeAddress(event) {
            const address = event.target.value;
            this.fullAddress = address;
            this.location.attributes.address = address;
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
