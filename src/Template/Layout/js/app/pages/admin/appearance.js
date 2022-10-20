export default {
    props: {
        configkey: {
            type: String,
        },
    },
    data() {
        return {
            config: {
                type: String,
            },
        };
    },
    mounted() {
        this.config = this.configkey || 'alert_message';
    },
};
