<script>
import Vue from 'vue';

/**
 * PlaceholderBus - Event bus for placeholder-related communications
 * Replaces EventBus for 'refresh-placeholders' and 'replace-placeholder' events
 *
 * This is a lightweight alternative to the global EventBus, scoped only to
 * placeholder functionality for better maintainability and Vue 3 compatibility.
 */
export const PlaceholderBus = new Vue({
    methods: {
        /**
         * Listen to an event
         * @param {string} event - Event name ('refresh-placeholders' or 'replace-placeholder')
         * @param {Function} callback - Callback function to invoke when event is triggered
         */
        listen(event, callback) {
            this.$on(event, callback);
        },

        /**
         * Send/emit an event
         * @param {string} event - Event name
         * @param {*} data - Data to pass to listeners
         */
        send(event, data) {
            this.$emit(event, data);
        },
    },
});

export default PlaceholderBus;
</script>
