/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Element/flash/flash.twig
 *
 * <flash-message> component
 *
 */
import { error as showError } from 'app/components/dialog/dialog';

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
        level: {
            type: String,
            default: '',
        },
        message: {
            type: String,
            default: '',
        },
        options: {
            type: Object,
            default: {}
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
        if (this.options?.modal) {
            this.isVisible = false;
            showError(this.message);
        }
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
