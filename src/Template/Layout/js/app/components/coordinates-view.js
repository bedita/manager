/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <coordinates-view> component used for Form
 *
 * Handle coordinates
 */
export default {
    template: `<div class="input text coordinates-content">
        <input type="hidden" name="coords" :value="pointValue" />
        <input placeholder="Long, Lat" type="text" :value="value" @change="update($event.target.value)" />
    </div>`,

    props: {
        coordinates: String,
    },

    data() {
        return {
            value: this.convertFromPoint(this.coordinates)
        };
    },

    computed: {
        pointValue() {
            return this.convertToPoint(this.value);
        },
    },

    async mounted() {
        this.$eventBus.$on('updatecoords', (point) => {
            this.value = `${point.lng}, ${point.lat}`;
        });
    },

    methods: {

        convertFromPoint(input) {
            if (!input) {
                return;
            }
            let match = input.match(/point\(([^)]*)\)/i);
            if (!match) {
                return;
            }
            return match[1].split(' ').join(', ');
        },

        convertToPoint(input) {
            if (!input) {
                return;
            }
            let [lon, lat] = input.split(/\s*,\s*/);

            return `POINT(${lon} ${lat})`;
        },

        update(value) {
            this.value = value;
        }
    },
};
