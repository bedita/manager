<script>

/**
 * PlaceholderBus - Event bus for placeholder-related communications
 * Replaces EventBus for 'refresh-placeholders' and 'replace-placeholder' events
 *
 * This is a lightweight alternative to the global EventBus, scoped only to
 * placeholder functionality for better maintainability and Vue 3 compatibility.
 */
const listeners = new Map();

export const PlaceholderBus = {
    /**
     * Listen to an event
     * @param {string} event - Event name ('refresh-placeholders' or 'replace-placeholder')
     * @param {Function} callback - Callback function to invoke when event is triggered
     */
    listen(event, callback) {
        const eventListeners = listeners.get(event) || new Set();
        eventListeners.add(callback);
        listeners.set(event, eventListeners);
    },

    /**
     * Send/emit an event
     * @param {string} event - Event name
     * @param {*} data - Data to pass to listeners
     */
    send(event, data) {
        listeners.get(event)?.forEach((callback) => callback(data));
    },
};

export default PlaceholderBus;
</script>
