<template>
    <div class="map-container">
    </div>
</template>
<script>
import mapbox from 'mapbox-gl';
import Compare from 'mapbox-gl-compare';
import 'mapbox-gl/dist/mapbox-gl.css';

export default {
    name: 'MapView',

    inject: {
        onCoordinatesUpdate: {
            from: 'onCoordinatesUpdate',
            default: null,
        },
    },

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

    async mounted() {
        this.$nextTick(() => {
            this.renderMapboxCompareMap();
        });
    },

    methods: {

        renderMapboxCompareMap() {
            Object.getOwnPropertyDescriptor(mapbox, 'accessToken').set(this.mapToken);

            const center = [this.lng, this.lat]; // [lng, lat]
            const point = [this.lng, this.lat];
            const popup = new mapbox.Popup()
                .setHTML(this.popupHtml);

            const before = document.createElement('div');
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
                const coords = marker.getLngLat();
                // Call provided callback if available
                if (this.onCoordinatesUpdate) {
                    this.onCoordinatesUpdate(coords);
                }
            });
        }
    }
}
</script>
