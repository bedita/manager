/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Element/flash/flash.twig
 *
 * <flash-message> component
 *
 */

export default {
    props: {
        timeout: {
            type: Number,
            default: 4,
        },
        isBlocking: {
            type: Boolean,
            default: false,
        },
        waitPanelAnimation: {
            type: Number,
            default: 0.5,
        },
    },

    data() {
        return {
            isVisible: true,
            isDumpVisible: false,
        };
    },

    mounted() {
        this.$nextTick(() => {
            if (!this.isBlocking) {
                setTimeout(() => {
                    this.hide();
                }, this.timeout * 1000);
            }
        });
    },

    methods: {
        hide() {
            this.isVisible = !this.isVisible;
            setTimeout(() => {
                this.$refs.flashMessagesContainer.remove();
                window._vueInstance.$emit('flash-message:closed');
            }, this.waitPanelAnimation * 1000);
        },
    },
}
