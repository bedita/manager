<script>
import Vue from 'vue';

export const EventBus = new Vue({
    methods: {
        listen(name, callback) {
            this.$on(name, callback);
        },
        send(name, data) {
            this.$emit(name, data);
        },
        stop(name, callback) {
            this.$off(name, callback);
        },
    }
});

export default EventBus;
</script>
