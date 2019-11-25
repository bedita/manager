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
 * @prop {String} popup (HTML content)
 * @prop {String} mapToken (mapBox accessToken)
 *
 */

import mapbox from 'mapbox-gl';
import Compare from 'mapbox-gl-compare';
import 'mapbox-gl/dist/mapbox-gl.css';

export default {
    template: `<div class="map-container">
        <div id='before' class='map'></div>
        <div id='after' class='map'></div>
    </div>`,

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
        this.renderMapboxCompareMap();

        // alternative simple map with no comparison
        // var map = new mapboxgl.Map({
        //     container: this.$el,
        //     style: 'mapbox://styles/mapbox/streets-v9',
        //     center: center,
        //     zoom: 13,
        // });
    },

    methods: {
        renderMapboxCompareMap() {
            Object.getOwnPropertyDescriptor(mapbox, "accessToken").set(this.mapToken);

            const center = [this.lng, this.lat]; // [lng, lat]
            const point = [this.lng, this.lat];
            const popup = new mapbox.Popup()
                .setHTML(this.popupHtml);

            const beforeMap = new mapbox.Map({
                container: 'before',
                style: 'mapbox://styles/mapbox/satellite-v9',
                center: center,
                zoom: 13,
            });

            const afterMap = new mapbox.Map({
                container: 'after',
                style: 'mapbox://styles/mapbox/streets-v9',
                center: center,
                zoom: 13,
            });

            const map = new Compare(beforeMap, afterMap, this.$el, {
                // Set this to enable comparing two maps by mouse movement:
                // mousemove: true
            }).setSlider(180);

            afterMap.addControl(new mapbox.NavigationControl());

            const afterMarker = new mapbox.Marker()
                .setLngLat(point)
                .addTo(afterMap);

            const beforeMarker = new mapbox.Marker({
                    color: '#d22551',
                })
                .setLngLat(point)
                .addTo(beforeMap);

            if (this.popupHtml) {
                afterMarker.setPopup(popup);
                beforeMarker.setPopup(popup);
            }
        }
    }
}
