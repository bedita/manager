/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Element/flash/flash.twig
 *
 * <flash-message> component
 *
 */
import { error, warning, info, success } from 'app/components/dialog/dialog';

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
            default: () => ({})
        },
        params: {
            type: Object,
            default: () => ({}),
        },
        viewName: {
            type: String,
            default: '',
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
        if (this.isModal) {
            this.isVisible = false;
            this.modalCtr(this.message);
        }
        this.$nextTick(() => {
            if (!this.isBlocking && !this.isModal) {
                setTimeout(() => {
                    this.hide();
                }, this.timeout * 1000);
            }
        });
    },

    computed: {
        isModal() {
            return this.options?.modal || false;
        },

        modalCtr() {
            switch (this.level) {
                case 'success':
                    return success;
                case 'error':
                    return error;
                case 'warning':
                    return warning;
                case 'info':
                default:
                    return info;
            }
        }
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
