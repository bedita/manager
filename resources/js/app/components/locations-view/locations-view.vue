<template>
    <div class="locations">
        <input type="hidden" :name="'relations[' + relationName + '][addRelated]'" :value="addRelatedData" />
        <input type="hidden" :name="'relations[' + relationName + '][removeRelated]'" :value="removeRelatedData" />
        <div v-if="!locations" class="is-loading-spinner"></div>
        <div v-else v-for="(location, index) in locations">
            <location-view
                :key="locationSymbol(location)"
                :index="index"
                :location-data="location"
                :api-key="apiKey"
                :api-url="apiUrl"
                :relation-name="relationName"
                :relation-label="relationLabel" />
        </div>
        <div v-if="locations" class="is-flex mt-1">
            <button @click.prevent @click="onAddNew">{{ msgAddNew }}</button>
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
    components: {
        LocationView: () => import(/* webpackChunkName: "location-view" */'app/components/locations-view/location-view'),
    },

    props: {
        apiKey: String,
        apiUrl: String,
        relationName: String,
        relationLabel: String,
    },

    data() {
        return {
            locations: null,
            originalParams: {},
            msgAddNew: t`add new`,
        }
    },

    async created() {
        const requestUrl = `${window.location.href}/related/${this.relationName}`;
        // if url contains view/related, it means that request comes from new object page: ignore it
        if (requestUrl.indexOf('view/related') >= 0) {
            this.locations = [];

            return;
        }
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
        // serialized to keep it unaffected by in-place edits of locations params
        this.originalParams = Object.fromEntries(
            this.locations.map((location) => [String(location.id), this.serializeParams(location)])
        );
    },

    async mounted() {
        this.$on('removed', (index) => {
            this.locations.splice(index, 1);
            this.markChanged();
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
            this.markChanged();
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
        },
        /**
         * Serialize relation params of a location
         * @param {Object} location
         * @return {String}
         */
        serializeParams(location) {
            return JSON.stringify(location?.meta?.relation?.params || {});
        },
        /**
         * Let the app know that something has changed here.
         * @return {void}
         */
        markChanged() {
            this.$el.dispatchEvent(new CustomEvent('change', {
                bubbles: true,
                detail: {
                    id: this.$vnode.tag,
                    isChanged: true,
                }
            }));
        }
    },

    computed: {
        addRelatedData() {
            const added = (this.locations || []).filter((location) => {
                const id = String(location.id);

                return !location.id || !(id in this.originalParams) || this.originalParams[id] !== this.serializeParams(location);
            });

            return JSON.stringify(added);
        },
        removeRelatedData() {
            const ids = (this.locations || []).map((location) => String(location.id));
            const removed = Object.keys(this.originalParams)
                .filter((id) => !ids.includes(id))
                .map((id) => ({ id, type: 'locations' }));

            return JSON.stringify(removed);
        },
    }
}
</script>
