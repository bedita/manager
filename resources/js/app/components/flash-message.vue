<template>
    <div id="flashMessagesContainer" ref="flashMessagesContainer" :class="isVisible ? 'on' : ''">

        <div class="background-mask" v-if="level === 'error' && viewName !== 'login'" v-show="isVisible" v-on:click.self="hide"></div>

        <div :class="['message', level, (params?.class || '').trim()]">
            <i class="icon-cancel-1 has-text-size-larger" v-if="viewName !== 'login'" @click="hide"></i>

            <h2>
                <i v-if="viewName !== 'login'" :class="iconClass"></i>
                {{ message }}
            </h2>

            <a v-if="shouldShowDump && isAdmin" @click="isDumpVisible = true" v-show="!isDumpVisible">
                {{ dumpLabel }}<i class="icon-down-dir"></i>
            </a>
            <div v-if="shouldShowDump && isAdmin" class="dump" v-show="isDumpVisible">
                <code v-html="dumpMessage"></code>
            </div>
            <p v-if="shouldShowDump && !isAdmin">{{ dumpLabel }}</p>
        </div>
    </div>
</template>

<script>
import { t } from 'ttag';
import { error, warning, info, success } from 'app/components/dialog/dialog';

export default {
    props: {
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
            default: () => ({}),
        },
        params: {
            type: Object,
            default: () => ({}),
        },
        timeout: {
            type: Number,
            default: 4,
        },
        userRoles: {
            type: Array,
            default: () => ([]),
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
            this.modalCtr(this.fullMessage());
        }
        this.$nextTick(() => {
            if (this.level !== 'error' && !this.isModal) {
                setTimeout(() => {
                    this.hide();
                }, this.timeout * 1000);
            }
        });
    },

    computed: {
        isAdmin() {
            return this.userRoles.includes('admin');
        },

        shouldShowDump() {
            return this.viewName !== 'login' && this.level === 'error';
        },

        dumpLabel() {
            if (this.params?.status || this.params?.code) {
                return `${t`code`}: ${this.params?.status || ''} ${this.params?.code || ''}`;
            }

            if (this.isAdmin) {
                return `${t`details`}:`;
            }

            return '';
        },

        dumpMessage() {
            if (!this.isAdmin) {
                return '';
            }

            return Object.entries(this.params)
                .map(([key, value]) => `${key}: ${value}`)
                .join('<br/>');
        },

        iconClass() {
            switch (this.level) {
                case 'success':
                    return 'icon-ok-circled-1';
                case 'error':
                    return 'icon-attention-circled';
                case 'warning':
                    return 'icon-dot-circled';
                case 'info':
                default:
                    return 'icon-info-1';
            }
        },

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
        fullMessage() {
            if (this.params && this.isAdmin) {
                return this.message + ' ' + Object.entries(this.params).map(([key, value]) => `${key}: ${value}`).join(' | ');
            }

            return this.message;
        },

        hide() {
            this.isVisible = !this.isVisible;
            setTimeout(() => {
                this.$refs.flashMessagesContainer.remove();
                window._vueInstance.$emit('flash-message:closed');
            }, this.waitPanelAnimation * 1000);
        },
    },
}
</script>
