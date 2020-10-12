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
        <div v-if="!locations" class="is-loading-spinner"></div>
        <div v-if="locations" v-for="(location, index) in locations">
            <location-view :key=index :index=index :locationdata=location :apikey="apikey" :apiurl="apiurl"/>
        </div>
        <div v-if="locations" class="buttons is-flex mt-1">
            <button @click.prevent @click="onAddNew"> <: t("add new") :> </button>
        </div>
    </div>`,

    props: {
        object: Object,
        apikey: String,
        apiurl: String,
    },

    data() {
        return {
            locations: null,
        }
    },

    async created() {
        const requestUrl = `${window.location.href}/relatedJson/has_location`;
        this.locations = (await (await fetch(requestUrl, options)).json()).data;

        // add params for location that does not have them
        this.locations.forEach((location) => {
            if (!location.meta || !location.meta.relation || !location.meta.relation.params) {
                location.meta = {
                    relation: {
                        params: {},
                    },
                };
            }
        });
    },

    async mounted() {
        this.$on('removed', (index) => {
            this.locations.splice(index, 1);
        });
    },

    methods: {
        onAddNew() {
            // create new empty location
            const newLocation = {
                attributes: {},
                type: 'locations',
                meta: {
                    relation: {
                        params: {},
                    },
                },
            };

            // add on view
            this.locations.push(newLocation);
        },
    },
}
