import Vue from 'vue';

export const AjaxLogin = Vue.extend({
    name: 'AjaxLogin',

    props: {
        headerText: {
            type: String,
            default: null,
        },
        message: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            isOpen: false,
            url: `${BEDITA.base}/login`,
        };
    },

    mounted() {
        window.addEventListener('message', this.checkLogin);
        this.isOpen = true;
    },

    methods: {
        checkLogin(message) {
            if (message.data !== 'login') {
                return;
            }

            this.$emit('login');
            this.close();
        },

        close() {
            this.isOpen = false;
            window.removeEventListener('message', this.checkLogin);

            if (this.$el.parentNode) {
                this.$el.parentNode.removeChild(this.$el);
            }

            this.$destroy();
        }
    },

    template: `
        <div class="ajax-login-modal" role="dialog">
            <transition name="fade">
                <div class="backdrop" @click="hide()" v-if="isOpen"></div>
            </transition>
            <transition name="slide">
                <div class="ajax-login">
                    <header>
                        <span class="title" v-if="headerText"><: headerText :></span>
                        <i class="icon-cancel-1 has-text-size-larger" @click="close()"></i>
                    </header>
                    <div class="content">
                        <div class="message" v-if="message">
                            <: message :>
                        </div>
                        <iframe title="Login form" :src="url" ref="iframe"></iframe>
                    </div>
                </div>
            </transition>
        </div>
    `,
});
