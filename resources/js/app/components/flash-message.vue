<template>
    <div id="flashMessagesContainer" ref="flashMessagesContainer" :class="isVisible ? 'on' : ''">

        <div class="background-mask" v-if="level === 'error' && viewName.toLowerCase() !== 'login'" v-show="isVisible" v-on:click.self="hide"></div>

        <div :class="['message', level, (params?.class || '').trim()]">
            <h2>
                <i v-if="viewName.toLowerCase() !== 'login'">
                    <Icon icon="carbon:checkmark" color="green" v-if="level === 'success'"></Icon>
                    <Icon icon="carbon:information" color="blue" v-if="level === 'info'"></Icon>
                    <Icon icon="carbon:warning" color="orange" v-if="level === 'warning'"></Icon>
                    <Icon icon="carbon:misuse" color="red" v-if="level === 'error'"></Icon>
                </i>
                {{ message }}
            </h2>

            <details v-if="shouldShowDump && isAdmin">
                <summary>{{ dumpLabel }}</summary>
                <pre class="dump">{{ dumpMessage }}</pre>
            </details>

            <p v-if="shouldShowDump && !isAdmin">{{ dumpLabel }}</p>

            <label v-if="viewName.toLowerCase() !== 'login'" @click="hide">
                <Icon icon="carbon:close-outline"></Icon>
                {{ t('Close') }}
            </label>
        </div>
    </div>
</template>

<script>
import { Icon } from '@iconify/vue2';
import { t } from 'ttag';
import { error, warning, info, success } from 'app/components/dialog/dialog';

export default {
    components: {
        Icon,
    },

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
            this.modalCtr(this.message, document.body, this.dumpMessage);
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
            return this.viewName.toLowerCase() !== 'login' && this.level === 'error' && !!this.dumpMessage;
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

            const isScalar = (value) => value === null || typeof value !== 'object';

            return Object.entries(this.params)
                .map(([key, value]) => `${key}: ${isScalar(value) ? value : JSON.stringify(value, null, 4)}`)
                .join('\n');
        },

        isModal() {
            return (this.viewName.toLowerCase() !== 'login' && this.options?.modal) || false;
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
