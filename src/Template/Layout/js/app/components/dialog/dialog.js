import { t } from 'ttag';
import Vue from 'vue';

/**
 * Component to display a dialog.
 */
export const Dialog = Vue.extend({
    template: `<div class="bedita-dialog" role="dialog">
        <transition name="fade">
            <div class="backdrop" @click="hide()" v-if="isOpen"></div>
        </transition>
        <transition name="slide">
            <div class="dialog p-1 has-background-white has-text-black" :class="dialogType" v-if="isOpen">
                <header class="is-flex space-between align-center is-expanded">
                    <div class="is-flex align-center">
                        <span class="is-capitalized mr-05" v-if="headerText"><: t(headerText) :></span>
                        <i :class="icon" class="has-text-size-larger" v-if="icon"></i>
                    </div>
                    <i class="icon-cancel-1 has-text-size-larger" @click="hide()"></i>
                </header>
                <div class="message mt-1 has-text-size-larger" v-if="message"><: message :></div>
                <input class="mt-1" type="text" v-if="dialogType == 'prompt'" v-model.lazy="inputValue" />
                <div class="actions mt-2">
                    <button class="button-outlined-white confirm mr-1" v-if="confirmMessage" @click="confirmCallback(inputValue, this);"><: confirmMessage :></button>
                    <button class="button-secondary cancel" @click="hide()" v-if="cancelMessage"><: cancelMessage :></button>
                </div>
            </div>
        </transition>
    </div>`,

    data() {
        return {
            destroyOnHide: true,
            isOpen: false,
            dialogType: 'warning',
            headerText: '',
            icon: 'icon-attention-circled',
            message: '',
            confirmMessage: 'ok',
            confirmCallback: this.hide,
            cancelMessage: t`cancel`,
            inputValue: '',
        };
    },
    methods: {
        show(message, headerText = this.dialogType, root = document.body) {
            this.headerText = headerText;
            this.message = message;
            this.isOpen = true;
            if (!this.$el) {
                this.$mount();
            }
            if (this.$el.parentNode !== root) {
                root.appendChild(this.$el);
            }
        },
        hide(destroy) {
            this.dialogType = '';
            this.isOpen = false;

            if (destroy != null ? destroy : this.destroyOnHide) {
                if (this.$el.parentNode) {
                    this.$el.parentNode.removeChild(this.$el);
                }
                this.$destroy();
            }
        },
        warning(message, root = document.body) {
            this.dialogType = 'warning';
            this.show(message);
        },
        error(message, root = document.body) {
            this.dialogType = 'error';
            this.show(message);
        },
        info(message, root = document.body) {
            this.dialogType = 'info';
            this.icon = 'icon-info-1';
            this.show(message, '');
        },
        confirm(message, confirmMessage, confirmCallback, type = 'warning', root = document.body) {
            this.dialogType = type;
            this.confirmMessage = confirmMessage;
            this.confirmCallback = confirmCallback;
            this.show(message);
        },
        prompt(message, defaultValue, confirmCallback, root = document.body) {
            this.dialogType = 'prompt';
            this.icon = 'icon-info-1';
            this.inputValue = defaultValue || '';
            this.confirmCallback = confirmCallback;
            this.show(message, '');
        },
    },
});

export const warning = (message, root) => {
    let dialog = new Dialog();
    dialog.warning(message, root);
    return dialog;
};

export const error = (message, root) => {
    let dialog = new Dialog();
    dialog.error(message, root);
    return dialog;
};

export const info = (message, root) => {
    let dialog = new Dialog();
    dialog.info(message, root);
    return dialog;
};

export const confirm = (message, confirmMessage, confirmCallback, type, root) => {
    let dialog = new Dialog();
    dialog.confirm(message, confirmMessage, confirmCallback, type, root);
    return dialog;
};

export const prompt = (message, defaultValue, confirmCallback, root) => {
    let dialog = new Dialog();
    dialog.prompt(message, defaultValue, confirmCallback, root);
    return dialog;
};
