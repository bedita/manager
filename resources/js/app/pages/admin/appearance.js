export default {
    components: {
        // icons
        IconLaunch: () => import(/* webpackChunkName: "icon-launch" */'@carbon/icons-vue/es/launch/16.js'),
        IconSave: () => import(/* webpackChunkName: "icon-save" */'@carbon/icons-vue/es/save/16.js'),
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
