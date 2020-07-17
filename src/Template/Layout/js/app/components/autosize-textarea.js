import autosize from "autosize";

export default {
    props: ["value", "reset-value"],

    template: `<textarea @input="handleChange" :value="text"></textarea>`,

    data() {
        return {
            text: '',
            originalValue: '',
        };
    },

    watch: {
        text() {
            // make sure render reactively
            this.$nextTick(() => {
                autosize(this.$el);
            });
        },

        value() {
            this.originalValue = this.value;
        }
    },

    mounted() {
        // setup initial value
        this.originalValue = this.value;
        this.text = this.value;

        // setup autosize
        this.$nextTick(() => {
            autosize(this.$el);
        });
    },

    methods: {
        /**
         *
         * @emits Event#input textarea value
         *
         * @param {Event} event event object
         */
        handleChange(event) {
            this.text = event.target.value;
            this.$emit('input', this.text);
        }
    }
};
