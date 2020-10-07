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
        <div v-for="(location, key) in locations">
            <location-view :index=key :locationdata=location :apikey="apikey" :apiurl="apiurl"/>
        </div>
        <div class="buttons is-flex mt-1">
            <button @click.prevent @click="onAddNew"> <: t("add new") :> </button>
            <button @click.prevent @click="onRemove" class="icon-unlink remove"> <: t("remove") :> </button>
        </div>
    </div>`,

    props: {
        object: Object,
        apikey: String,
        apiurl: String,
        addedRelationsData: [Array, String],
        removedRelationsData: [Array, String],
    },

    data() {
        return {
            locations: [],
            added: this.addedRelationsData,
            removed: this.removedRelationsData,
        }
    },

    async created() {
        const requestUrl = `${window.location.href}/relatedJson/has_location`;
        this.locations = (await (await fetch(requestUrl, options)).json()).data;

        this.locations.forEach((location) => {
            if (!location.meta || !location.meta.relation || !location.meta.relation.params) {
                location.meta = {
                    relation: {
                        params: {},
                    },
                };
            }
        });

        // API that saves relation data needs the following format
        const transformLocationForApi = (location) => {
            return {
                id: location.id,
                type: 'locations',
                meta: location.meta,
            };
        };

        this.$on('modified', async (location) => {
            // save location if not already saved
            if (!location.id) {
                location.type = 'locations';

                console.log('location', location)
                const requestUrl = `${BEDITA.base}/api/locations`;
                const response = await fetch(requestUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-Token': BEDITA.csrfToken,
                    },
                    body: JSON.stringify({
                        data: location,
                    }),
                });
                console.log('response', response)
            }

            location = transformLocationForApi(location);

            if (!this.added || !this.added.length) {
                this.added.push(location);
                return;
            }

            this.added.forEach((data) => {
                const dataID = data.id || data.attributes.id;
                const locationID = location.id;

                if (dataID == locationID) {
                    data = transformLocationForApi(location);
                }
            })

            this.$parent.$emit('locations-modified', this.added, this.removed);
        });

        // add available locations on relation model
        this.added.push(...this.locations);
    },
    methods: {
        onAddNew() {
            // create new empty location
            const newLocation = {
                attributes: {},
                meta: {
                    relation: {
                        params: {},
                    },
                },
            };

            // add on view
            this.locations.push(newLocation);
        },
        onRemove() {
            // retrieve last relation (now remove button removes the last one)
            const removedLocation = this.locations[this.locations.length - 1];

            // add this location to the array of removed locations
            this.removed.push(transformLocationForApi(removedLocation));

            this.$parent.$emit('locations-modified', this.added, this.removed);

            // remove it also from view
            this.locations.pop({});
        },
    },
}
