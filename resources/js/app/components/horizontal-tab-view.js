/**
 * Templates that uses this component (directly or indirectly):
 *  ...
 *
 * <horizontal-tab-view> component used for ModulesPage -> View
 *
 * Handle horizontal tabs
 *
 * @prop {Array} labels
 * @prop {int} defaultActive index of the default active label in labels
 *
 */

export default {
    components: {
        FormFileUpload: () => import(/* webpackChunkName: "form-file-upload" */'app/components/form-file-upload'),
        // icons
        IconUpload: () => import(/* webpackChunkName: "icon-upload" */'@carbon/icons-vue/es/upload/16.js'),
    },

    props: {
        labels: {
            type: Array,
            default: [],
        },
        defaultActive: {
            type: Number,
            default: 0,
        },
    },

    data() {
        return {
            activeIndex: this.defaultActive,
        }
    },

    methods: {
    }
}
