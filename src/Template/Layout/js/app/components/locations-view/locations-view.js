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
            <location-view :index=key :location=location />
        </div>
        <div class="buttons is-flex mt-1">
            <button @click.prevent @click="onAddNew"> <: t("add new") :> </button>
            <button @click.prevent @click="onRemove" class="icon-unlink remove"> <: t("remove") :> </button>
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
        const requestUrl = `${window.location.href}/relatedJson/has_location`;
        this.locations = (await (await fetch(requestUrl, options)).json()).data;
    },
    methods: {
        onAddNew() {
            this.locations.push({});
        },
        onRemove() {
            this.locations.pop({});
        },
    },
}
