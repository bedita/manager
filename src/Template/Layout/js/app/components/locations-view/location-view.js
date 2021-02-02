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
        <input type="hidden" :name="'relations[' + relationName + '][replaceRelated][]'" :value="locationValue" />
        <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
            <: index + 1 :>
        </div>
        <div class="location-form is-flex-column">
            <div class="is-flex">
                <div class="is-flex-column is-expanded">
                    <label>
                        <: t('Title') :>
                        <autocomplete
                            autocomplete="none"
                            ref="title"
                            class="autocomplete-title"
                            :default-value="getDefaultTitle()"
                            :search="searchTitle"
                            base-class="autocomplete-title"
                            :get-result-value="getTitle"
                            @submit="onSubmitTitle"
                            @input="onInputTitle"
                            @change="onChange"
                            :debounce-time="500"
                        >
                        </autocomplete>
                    </label>
                </div>
                <div class="is-flex-column is-expanded">
                    <label>
                        <: t('Address') :>
                        <autocomplete
                            autocomplete="none"
                            ref="address"
                            class="autocomplete-address"
                            :default-value="getDefaultAddress()"
                            :search="searchAddress"
                            base-class="autocomplete-address"
                            :get-result-value="getAddress"
                            @submit="onSubmitAddress"
                            @input="onInputAddress"
                            @change="onChange"
                            :debounce-time="500"
                        >
                        </autocomplete>
                    </label>
                </div>
            </div>
            <div class="is-flex mt-1">
                <div class="is-flex-column is-expanded">
                    <label>
                        <: t('Long Lat Coordinates') :>
                        <div class="is-flex">
                            <input class="coordinates" type="text" :value="coordinates" @change="onCoordsChange" />
                            <button class="get-coordinates icon-globe" @click.prevent="geocode" :disabled="!apikey || !location.attributes.address">
                                <: t('GET') :>
                            </button>
                        </div>
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        Zoom
                        <input @change="onRelationDataChange" :value="zoom" data-name="zoom" type="number" min="2" max="20"/>
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        Pitch°
                        <input @change="onRelationDataChange" :value="pitch" data-name="pitch" type="number" min="0" max="60"/>
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        Bearing°
                        <input @change="onRelationDataChange" :value="bearing" data-name="bearing" type="number" min="-180" max="180"/>
                    </label>
                </div>
            </div>
            <div class="location-buttons">
                <button @click.prevent @click="onRemove" class="icon-unlink remove"><: t("remove") :></button>
            </div>
        </div>
    </div>`,

    props: {
        index: Number,
        apikey: String,
        apiurl: String,
        locationdata: Object,
        relationName: String,
    },

    data() {
        return {
            location: this.locationdata,
            title: this.locationdata.attributes.title,
            coordinates: convertFromPoint(this.locationdata.attributes.coords),
            zoom: parseInt(this.locationdata.meta &&
                this.locationdata.meta.relation &&
                this.locationdata.meta.relation.params &&
                this.locationdata.meta.relation.params.zoom) || 2,
            pitch: parseInt(this.locationdata.meta &&
                this.locationdata.meta.relation &&
                this.locationdata.meta.relation.params &&
                this.locationdata.meta.relation.params.pitch) || 0,
            bearing: parseInt(this.locationdata.meta &&
                this.locationdata.meta.relation &&
                this.locationdata.meta.relation.params &&
                this.locationdata.meta.relation.params.bearing) || 0,
        }
    },

    computed: {
        locationValue() {
            return JSON.stringify({
                id: this.location && this.location.id,
                type: 'locations',
                attributes: {
                    status: 'on',
                    ...(this.location && this.location.attributes || {}),
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
        /**
         * Check if a location's `attrName` already appears between a list of locations.
         * In that case append its `suffixAttr` as suffix to be more distinguishable.
         * Do it for both
         * @param {Object} location The location to process
         * @param {array} locations The locations list
         * @param {string} attrName The attribute name to search duplicates for
         * @param {string} suffixAttr The attribute name to use as suffix
         */
        applySuffix(location, locations, attrName, suffixAttr) {
            if (!location.attributes[suffixAttr] || !this.fetchedLocations) {
                return;
            }

            let duplicateValueIdx = this.fetchedLocations.findIndex((rawLocation) =>
                rawLocation.id != location.id &&
                rawLocation.attributes[attrName].toLowerCase() === location.attributes[attrName].toLowerCase()
            );
            if (duplicateValueIdx == -1) {
                return;
            }

            location.attributes[attrName] = stripHtml(`${location.attributes[attrName]} (${location.attributes[suffixAttr]})`);

            const duplicateValue = this.fetchedLocations[duplicateValueIdx];
            if (!duplicateValue.attributes[suffixAttr]) {
                return;
            }

            // if `suffixAttr` is set, append the value also for the duplicate
            locations[duplicateValueIdx].attributes[attrName] = stripHtml(`${duplicateValue.attributes[attrName]} (${duplicateValue.attributes[suffixAttr]})`);
        },
        onChange() {
            this.$parent.$emit('updated', this.index, this.location);
        },
        onSubmitTitle(result) {
            // use original fetched location instead of the "potentially" edited one (see `searchAddress` method)
            let location = this.fetchedLocations.find((item) => item.id == result.id);
            this.location = location; // set the retrieved location as model
            this.coordinates = convertFromPoint(this.location.attributes.coords);
            this.$parent.$emit('updated', this.index, this.location);
        },
        onSubmitAddress(result) {
            // use original fetched location instead of the "potentially" edited one (see `searchAddress` method)
            let location = this.fetchedLocations.find((item) => item.id == result.id);
            this.location = location; // set the retrieved location as model
            this.coordinates = convertFromPoint(this.location.attributes.coords);
            this.$parent.$emit('updated', this.index, this.location);
        },
        onInputTitle(event) {
            const title = event.target.value;
            this.location.attributes.title = title;
        },
        onInputAddress(event) {
            const address = event.target.value;
            this.location.attributes.address = address;
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

                        results.forEach((location) => this.applySuffix(location, results, 'title', 'address'));

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
                            const address = this.address(location);
                            return address && address.toLowerCase().indexOf(input.toLowerCase()) !== -1;
                        });
                        // store raw fetched data
                        this.fetchedLocations = results.slice();

                        results.forEach((location) => this.applySuffix(location, results, 'address', 'title'));

                        resolve(results);
                    });
            });
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
            if (!model || !model.attributes || !model.attributes.address) {
                return '';
            }

            return stripHtml(`${model.attributes.address}`);
        },
        async geocode() {
            const retrieveGeocode = () => {
                if (!window.google || !window.google.maps) {
                    return;
                }

                const geocoder = new window.google.maps.Geocoder();
                geocoder.geocode({ address: this.address(this.location) }, (results, status) => {
                    if (status === "OK" && results.length) {
                        const result = results[0];

                        // Longitude, Latitude format: see https://docs.mapbox.com/api/#coordinate-format
                        this.coordinates = `${result.geometry.location.lng()}, ${result.geometry.location.lat()}`;
                        this.location.attributes.coords = convertToPoint(this.coordinates);
                        this.$parent.$emit('updated', this.index, this.location);
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
            script.id = 'googleapi';

            window.initMap = () => {
                retrieveGeocode();
            };
            document.head.appendChild(script);
        },
        onCoordsChange(event) {
            this.location.attributes.coords = convertToPoint(event.target.value);
            this.onChange();
        },
        onRelationDataChange(event) {
            const target = event.target;
            const value = target.value;
            const attributeName = target.dataset['name'];
            this[attributeName] = parseInt(value);

            this.location.meta.relation.params.zoom = `${this.zoom}`;
            this.location.meta.relation.params.pitch = `${this.pitch}`;
            this.location.meta.relation.params.bearing = `${this.bearing}`;
            this.$parent.$emit('updated', this.index, this.location);
        },
        onRemove() {
            this.$parent.$emit('removed', this.index);
        },
    }
}
