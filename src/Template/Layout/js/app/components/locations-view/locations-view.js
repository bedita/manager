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
        addedRelationsData: Array,
        removedRelationsData: Array,
    },

    data() {
        return {
            locations: [],
        }
    },

    async created() {
        const requestUrl = `${window.location.href}/relatedJson/has_location`;
        this.locations = (await (await fetch(requestUrl, options)).json()).data;

        this.locations.forEach((location) => {
            if (!location.meta || !location.meta.relation || !location.meta.relation.params) {
                location.meta.relation.params = {};
            }
        });

        this.$on('modified',(location) => {
            if (!this.addedRelationsData || !this.addedRelationsData.length) {
                this.addedRelationsData.push(location);

                this.$parent.$emit('locations-modified', this.addedRelationsData, this.removedRelationsData);
                return;
            }

            let foundLocation = this.addedRelationsData.filter((elem) => elem.attributes.id === location.attributes.id);
            if (foundLocation) {
                foundLocation = location;

                this.$parent.$emit('locations-modified', this.addedRelationsData, this.removedRelationsData);
                return;
            }
        });

        // add available locations on relation model
        this.addedRelationsData.push(...this.locations);
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
            this.removedRelationsData.push(removedLocation);

            this.$parent.$emit('locations-modified', this.addedRelationsData, this.removedRelationsData);

            // remove it also from view
            this.locations.pop({});
        },
    },
}
