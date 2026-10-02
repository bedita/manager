const listeners = new Map();

export const AppEvents = {
    on(event, callback) {
        const eventListeners = listeners.get(event) || new Set();
        eventListeners.add(callback);
        listeners.set(event, eventListeners);
    },

    off(event, callback) {
        const eventListeners = listeners.get(event);
        eventListeners?.delete(callback);
        if (eventListeners?.size === 0) {
            listeners.delete(event);
        }
    },

    once(event, callback) {
        const onceCallback = (payload) => {
            this.off(event, onceCallback);
            callback(payload);
        };
        this.on(event, onceCallback);
    },

    emit(event, payload) {
        listeners.get(event)?.forEach((callback) => callback(payload));
    },
};
