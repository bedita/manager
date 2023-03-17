import { Icon } from '@iconify/vue2';

export default {
    components: {
        Icon,
    },
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
