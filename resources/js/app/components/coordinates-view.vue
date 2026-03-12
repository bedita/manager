<template>
    <div class="input text coordinates-content is-flex">
        <div class="is-expanded">
            <input
                id="coords"
                type="hidden"
                name="coords"
                :value="pointValue"
            >
            <input
                :id="id"
                class="coordinates"
                placeholder="Long, Lat"
                type="text"
                :value="value"
                @change="update($event.target.value)"
            >
        </div>
        <div>
            <button
                class="button button-primary get-coordinates"
                @click.prevent="getCoordinates"
                v-if="googleOptions?.apiKey"
            >
                <app-icon icon="carbon:wikis" />
                <span class="ml-05">{{ msgGet }}</span>
            </button>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'CoordinatesView',

    inject: {
        registerCoordinatesListener: {
            from: 'registerCoordinatesListener',
            default: null,
        },
    },

    props: {
        coordinates: {
            type: String,
            default: '',
        },
        options: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            googleOptions: {},
            id: {
                type: String,
                default: '',
            },
            msgGet: t`GET`,
            msgMissingSearchAddress: t`Missing data to search coordinates: insert at least "Address" and "Locality" (optional: "Zipcode", "Country", "Region")`,
            value: this.$helpers.convertFromPoint(this.coordinates)
        };
    },

    computed: {
        pointValue() {
            return this.$helpers.convertToPoint(this.value);
        },
    },

    async mounted() {
        const updateHandler = (point) => {
            this.value = `${point.lng}, ${point.lat}`;
        };

        // Register with provided callback
        if (this.registerCoordinatesListener) {
            this.registerCoordinatesListener(updateHandler);
        }

        const options = JSON.parse(this.options);
        this.googleOptions = {
            apiKey: options.key,
            apiUrl: options.url,
        };
        this.id = 'coordinates-' + Math.random().toString(36);
    },

    methods: {

        getSearchAddress() {
            let search = '';
            const address = document.getElementById('address')?.value || '';
            if (!address) {
                return search;
            }
            const locality = document.getElementById('locality')?.value || '';
            if (!locality) {
                return search;
            }
            search = `${address}, ${locality}`;
            const zipcode = document.getElementById('zipcode')?.value || '';
            if (zipcode) {
                search += `, ${zipcode}`;
            }
            const country = document.getElementById('country')?.value || '';
            if (country) {
                search += `, ${country}`;
            }
            const region = document.getElementById('region')?.value || '';
            if (region) {
                search += `, ${region}`;
            }

            return search;
        },

        async getCoordinates() {
            const address = this.getSearchAddress();
            if (!address) {
                BEDITA.warning(this.msgMissingSearchAddress);

                return;
            }
            const coords = document.getElementById(this.id);
            if (!coords) {

                return;
            }
            await this.geocode(
                address,
                coords,
                this.googleOptions,
            );
        },

        update(value) {
            this.value = value;
        },

        async geocode(address, coordinatesTargetElement, options) {
            const retrieveGeocode = () => {
                if (!window.google || !window.google.maps) {
                    return;
                }
                const geocoder = new window.google.maps.Geocoder();
                geocoder.geocode({ address }, (results, status) => {
                    if (status === 'OK' && results.length) {
                        const result = results[0];
                        // Longitude, Latitude format: see https://docs.mapbox.com/api/#coordinate-format
                        const val = `${result.geometry.location.lng()}, ${result.geometry.location.lat()}`;
                        coordinatesTargetElement.value = val;
                        this.update(val);
                    } else {
                        coordinatesTargetElement.value = '';
                        this.update('');
                    }
                });
            };
            if (document.querySelector('#googleapi')) {
                return retrieveGeocode();
            }
            // init script
            const script = document.createElement('script');
            script.src = `${options.apiUrl}js?key=${options.apiKey}&callback=initMap`;
            script.defer = true;
            script.id = 'googleapi';
            window.initMap = () => {
                retrieveGeocode();
            };
            document.head.appendChild(script);
        },
    },
};
</script>
<style scoped>
input.coordinates {
    width: 100%;
}
</style>
