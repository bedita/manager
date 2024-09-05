<template>
    <div class="location mb-2 is-flex">
        <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
            {{ index + 1 }}
        </div>
        <div class="location-form is-flex-column">
            <div class="is-flex">
                <div class="is-flex-column is-expanded">
                    <label>
                        {{ msgTitle }}
                        <autocomplete
                            autocomplete="none"
                            ref="title"
                            class="autocomplete-title"
                            :default-value="title"
                            :search="searchTitle"
                            base-class="autocomplete-title"
                            :get-result-value="getResultTitle"
                            :debounce-time="500"
                            :disabled="!!id"
                            @submit="onAutocompleteSubmit"
                            @input="onInputTitle"
                            @change="onChange"
                        />
                    </label>
                </div>
                <div class="is-flex-column is-expanded">
                    <label>
                        {{ msgAddress }}
                        <autocomplete
                            autocomplete="none"
                            ref="address"
                            class="autocomplete autocomplete-address"
                            :default-value="address"
                            :search="searchAddress"
                            base-class="autocomplete-address"
                            :get-result-value="getResultAddress"
                            :debounce-time="500"
                            :disabled="!!id"
                            @submit="onAutocompleteSubmit"
                            @input="onInputAddress"
                            @change="onChange"
                        />
                    </label>
                </div>
            </div>
            <div class="is-flex mt-1">
                <div class="is-flex-column is-expanded">
                    <label>
                        {{ msgCoordinates }}
                        <div class="is-flex">
                            <input
                                class="coordinates"
                                type="text"
                                :disabled="!!id"
                                v-model="coordinates"
                                @change="onChange"
                            >
                            <button
                                class="get-coordinates"
                                :disabled="!!location.id || !address"
                                @click.prevent="geocode"
                                v-if="apiKey"
                            >
                                <app-icon icon="carbon:wikis" />
                                <span class="ml-05">{{ msgGet }}</span>
                            </button>
                        </div>
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        {{ msgZoom }}
                        <input
                            type="number"
                            min="2"
                            max="20"
                            v-model.number="zoom"
                            @change="onChange"
                        >
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        {{ msgPitch }}°
                        <input
                            type="number"
                            min="0"
                            max="60"
                            v-model.number="pitch"
                            @change="onChange"
                        >
                    </label>
                </div>
                <div class="is-flex-column">
                    <label>
                        {{ msgBearing }}°
                        <input
                            type="number"
                            min="-180"
                            max="180"
                            v-model.number="bearing"
                            @change="onChange"
                        >
                    </label>
                </div>
            </div>
            <div class="location-buttons">
                <a class="button button-text-white"
                   :href="$helpers.buildViewUrl(id)"
                   target="_blank"
                   v-if="id"
                >
                    <app-icon icon="carbon:launch" />
                    <span class="ml-05">{{ msgEdit }}</span>
                </a>
                <button class="button button-text-white remove"
                        @click.prevent="onRemove"
                >
                    <app-icon icon="carbon:unlink" />
                    <span class="ml-05">{{ msgRemove }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

const options = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

export default {
    props: {
        index: {
            type: Number,
            default: null,
        },
        apiKey: {
            type: String,
            default: '',
        },
        apiUrl: {
            type: String,
            default: '',
        },
        locationData: {
            type: Object,
            default: () => ({}),
        },
        relationName: {
            type: String,
            default: '',
        },
        relationLabel: {
            type: String,
            default: '',
        },
    },

    data() {
        const data = this.locationData || {};
        const params = data?.meta?.relation?.params || {};

        return {
            location: data,
            id: data?.id || null,
            title: data?.attributes?.title,
            address: data?.attributes?.address,
            coordinates: this.$helpers.convertFromPoint(data?.attributes?.coords),
            zoom: this.getParamValue(params.zoom),
            pitch: this.getParamValue(params.pitch),
            bearing: this.getParamValue(params.bearing),
            msgAddress: t`Address`,
            msgBearing: t`Bearing`,
            msgCoordinates: t`Long Lat Coordinates`,
            msgEdit: t`edit`,
            msgGet: t`GET`,
            msgPitch: t`Pitch`,
            msgRemove: t`remove`,
            msgTitle: t`Title`,
            msgZoom: t`Zoom`,
        }
    },

    methods: {
        getParamValue(val) {
            if (!parseInt(val)) {
                return null;
            }

            return parseInt(val);
        },
        onChange() {
            this.location.attributes.title = this.title;
            this.location.attributes.address = this.address;
            this.location.attributes.status = 'on';
            this.location.attributes.coords = this.$helpers.convertToPoint(this.coordinates);
            this.location.meta.relation = this.location.meta.relation || { params: {} }; // ensure relation object
            // cast to string, otherwise API will return 400
            this.location.meta.relation.params.zoom = `${this.getParamValue(this.zoom)}`;
            this.location.meta.relation.params.pitch = `${this.getParamValue(this.pitch)}`;
            this.location.meta.relation.params.bearing = `${this.getParamValue(this.bearing)}`;

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
            this.coordinates = this.$helpers.convertFromPoint(this.location.attributes.coords);
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

            return this.$helpers.stripHtml(`${address.trim()}`);
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
</script>
