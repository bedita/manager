/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <coordinates-view> component used for Form
 *
 * Handle coordinates
 */
export default {
    template: `
        <input type="hidden" name="coordinates" :value="pointValue" />
        <input type="text" :value="value" />
    `,

    props: {
        coordinates: String,
    },

    data() {
        return {
            value: this.convertFromPoint(this.coordinates),
        }
    },

    computed: {
        pointValue() {
            return this.convertToPoint(this.value);
        },
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
            let [lon, lat] = match[1].split(' ');
            return `${lat}, ${lon}`;
        },

        convertToPoint(input) {
            if (!input) {
                return;
            }
            let [lat, lon] = input.split(/\s*,\s*/);
            return `POINT(${lon} ${lat})`;
        }
    },
}
