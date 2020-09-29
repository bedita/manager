import { t } from 'ttag';

/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <location-view> component used for ModulesPage -> View
 *
 * Handle Locations and reverse geocoding from addresses
 *
 *
 */
export default {
    template: `
        <div class="location is-flex">
            <div class="order mr-1 p-1 has-background-white is-flex align-center has-text-black">
                <: index + 1 :>
            </div>
            <div class="location-form is-flex-column">
                <div class="is-flex">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Title') :>
                            <input @change="onChange" type="text"/>
                        </label>
                    </div>
                    <div class="is-flex-column is-expanded">
                        <label><: t('Address') :>
                            <input @change="onChange" type="text"/>
                        </label>
                    </div>
                </div>
                <div class="is-flex mt-1">
                    <div class="is-flex-column is-expanded">
                        <label><: t('Long Lat Coordinates') :>
                            <div class="is-flex">
                                <input class="coordinates" @change="onChange" type="text"/>
                                <button disabled class="get-coordinates icon-globe">
                                    <: t('GET') :>
                                </button>
                            </div>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Zoom
                            <input @change="onChange" type="number" min="2" max="20"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Pitch°
                            <input @change="onChange" type="number" min="0" max="60"/>
                        </label>
                    </div>
                    <div class="is-flex-column">
                        <label> Bearing°
                            <input @change="onChange" type="number" min="-180" max="180"/>
                        </label>
                    </div>
                </div>
                <div class="buttons is-flex mt-1">
                    <button :disabled="!pristine"> <: t("add new") :> </button>
                    <button class="icon-unlink remove" :disabled="!pristine"> <: t("remove") :> </button>
                </div>
            </div>
        </div>`,

    props: {
        index: Number,
        location: Object,
    },

    data() {
        return {
            pristine: true,
        }
    },

    async created() {
    },

    methods: {
        onChange() {

        }
    }
}
