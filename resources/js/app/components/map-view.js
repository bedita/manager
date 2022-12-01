/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <map-view> component used for ModulesPage -> View
 *
 * Handle maps
 *
 * @prop {String} lat
 * @prop {String} lng
 * @prop {String} popupHtml
 * @prop {String} mapToken (mapBox accessToken)
 */

import mapbox from 'mapbox-gl';
import Compare from 'mapbox-gl-compare';
import 'mapbox-gl/dist/mapbox-gl.css';

export default {
    template: `<div class="map-container"></div>`,

    props: {
        lng: {
            type: String,
            default: '0',
        },
        lat: {
            type: String,
            default: '0',
        },
        popupHtml: {
            type: String,
            default: '',
        },
        mapToken: {
            type: String,
            default: '',
        }
    },

    data() {
        return {}
    },

    async mounted() {
        this.$nextTick(() => {
            this.renderMapboxCompareMap();
        });
    },

    methods: {

        renderMapboxCompareMap() {
            Object.getOwnPropertyDescriptor(mapbox, "accessToken").set(this.mapToken);

            const center = [this.lng, this.lat]; // [lng, lat]
            const point = [this.lng, this.lat];
            const popup = new mapbox.Popup()
                .setHTML(this.popupHtml);

            const before = document.createElement("div");
            before.classList.add('compare-map');
            const after = before.cloneNode(false);
            const over = before.cloneNode(false);

            this.$el.append(before, after, over);

            const beforeMap = new mapbox.Map({
                container: before,
                style: 'mapbox://styles/mapbox/satellite-v9',
                center: center,
                zoom: 13,
            });

            const afterMap = new mapbox.Map({
                container: after,
                style: 'mapbox://styles/mapbox/streets-v9',
                center: center,
                zoom: 13,
            });

            // controls
            afterMap.addControl(new mapbox.NavigationControl());

            // compare slider
            new Compare(beforeMap, afterMap, this.$el).setSlider(180);

            // over map with empty tiles for markers and popup
            const overMap = new mapbox.Map({
                container: over,
                center: center,
                zoom: 13,
            });

            const marker = new mapbox.Marker({
                color: '#d22551',
                draggable: true,
            })
                .setLngLat(point)
                .addTo(overMap);

            if (this.popupHtml) {
                marker.setPopup(popup);
            }

            marker.on('dragend', () => {
                this.$eventBus.$emit('updatecoords', marker.getLngLat());
            });
        }
    }
}
