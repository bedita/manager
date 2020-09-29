import { t } from 'ttag';

/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <locations-view> component used for ModulesPage -> View
 *
 * Handle Locations and reverse geocoding from addresses
 *
 *
 */
export default {
    template: `<div class="locations">
        <div v-for="(location, key) in locations.data">
            <div class="location is-flex">
                <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black"> <: key + 1 :> </div>
                <div class="location-form is-flex-column">
                    <div class="is-flex">
                        <div class="is-flex-column is-expanded">
                            <label><: t('Title') :>
                                <input type="text"/>
                            </label>
                        </div>
                        <div class="is-flex-column is-expanded">
                            <label><: t('Address') :>
                                <input type="text"/>
                            </label>
                        </div>
                    </div>
                    <div class="is-flex mt-1">
                        <div class="is-flex-column is-expanded">
                            <label><: t('Long Lat Coordinates') :>
                                <input type="text"/>
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
            </div>
        </div>
    </div>`,

    props: {
        object: Object,
    },

    data() {
        return {
            locations: [],
        }
    },

    async created() {
        const options = {
            credentials: 'same-origin',
            headers: {
                'accept': 'application/json',
            }
        };
        const requestUrl = `${window.location.href}/relatedJson/has_location`;
        this.locations = await (await fetch(requestUrl, options)).json();
    },

    methods: {
        renderMapboxCompareMap() {

        }
    }
}
