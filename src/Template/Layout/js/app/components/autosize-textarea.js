import autosize from "autosize";

export default {
    props: ["value"],

    template: `<textarea @input="handleChange" :value="text"></textarea>`,

    data() {
        return {
            text: '',
        };
    },

    watch: {
        text() {
            // make sure render reactively
            this.$nextTick(() => {
                autosize(this.$el);
            });
        }
    },

    mounted() {
        // setup initial value
        this.text = this.value;
    },

    methods: {
        /**
         *
         * @emits Event#input textarea value
         *
         * @param {Event} event event object
         */
        handleChange(evente) {
            this.text = event.target.value;
            this.$emit('input', this.text);
        }
    }
};
