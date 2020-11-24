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
 */
export default {
    components: {
        LocationView: () => import(/* webpackChunkName: "location-view" */'app/components/locations-view/location-view'),
    },

    template: `<div class="locations">
        <div v-if="!locations" class="is-loading-spinner"></div>
        <div v-if="locations" v-for="(location, index) in locations">
            <location-view :key="locationSymbol(location)" :index="index" :locationdata="location" :apikey="apikey" :apiurl="apiurl" :relation-name="relationName" />
        </div>
        <div v-if="locations" class="is-flex mt-1">
            <button @click.prevent @click="onAddNew"><: t('add new') :></button>
        </div>
    </div>`,

    props: {
        apikey: String,
        apiurl: String,
        relationName: String,
        object: Object,
    },

    data() {
        return {
            locations: null,
        }
    },

    async created() {
        const requestUrl = `${window.location.href}/relatedJson/${this.relationName}`;
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
        this.$on('updated', (index, location) => {
            if (!location.meta || !location.meta.relation || !location.meta.relation.params) {
                location.meta = {
                    relation: {
                        params: {},
                    },
                };
            }
            this.locations.splice(index, 1, location);
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
        /**
         * Provide an unique key vor the v-for
         * @param {Object} location
         * @return {Object}
         */
        locationSymbol(location) {
            return Symbol(location);
        }
    },
}
