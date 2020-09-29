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
        <div v-for="location in locations.data">
            <div class="location">
                <: location.attributes.title :>
            </div>
        </div>
    </div>`,

    props: {
        object: {
            type: Object,
        },
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
