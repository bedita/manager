/**
 * Filter Type View component
 *
 * allows to filter a list of objects with a text field, properties and a pagination toolbar
 *
 * <filter-type-view> component
 *
 * @prop {Object} relationTypes relation types available for relation (left/right)
 */

export default {
    props: {
        types: {
            type: Array,
            default: [],
        },
    },

    data() {
        return {
            selectedTypes: () => [],
        };
    },

    mounted() {
        let params = new URLSearchParams(window.location.search);
        let entries = params.entries();
        let types = [];
        for(let entry of entries) { // each 'entry' is a [key, value]
            var key = entry[0];
            var val = entry[1];
            if (key.startsWith('filter[type]')) {
                types.push(val);
            }
        }
        this.selectedTypes = types;
    },

    computed: {
    },

    watch: {
        selectedTypes(value) {
            this.$emit('updatequerytypes', value);
        }
    },

    methods: {
        toggleAll() {
            if (this.selectedTypes.length) {
                this.selectedTypes = [];
            }
        },
    }
};
