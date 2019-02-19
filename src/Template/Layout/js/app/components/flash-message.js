/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Element/Flash/default.twig
 *
 * <flash-message> component
 *
 */

export default {
    props: {
        timeout: {
            type: Number,
            default: 10,
        },
    },

    data() {
        return {
            visibilityClass: 'on',
            showDetails: false,
        };
    },

    mounted() {
        // if (this.$el.classList.contains('error')) {
        //     return;
        // }
        // this.visibilityClass = 'on';
        // setTimeout(() => {
        //     this.$nextTick(() => {
        //         this.closeMessage();
        //     });
        // }, this.timeout * 1000);
    },

    methods: {
        closeMessage() {
            if (this.visibilityClass === 'on') {
                this.$el.parentNode.removeChild(this.$el);
                this.visibilityClass = '';
            }
        },
    },
}
