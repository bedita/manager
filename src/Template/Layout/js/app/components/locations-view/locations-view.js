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
    components: {
        LocationView: () => import(/* webpackChunkName: "location-view" */'app/components/locations-view/location-view'),
    },

    template: `<div class="locations">
        <div v-for="(location, key) in locations.data">
            <location-view :index=key :location=location />
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
    methods: {},
}
