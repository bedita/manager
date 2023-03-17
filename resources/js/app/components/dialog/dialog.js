import { Icon } from '@iconify/vue2';
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
                        <Icon icon="carbon:checkmark" color="green" v-if="dialogType === 'success'"></Icon>
                        <Icon icon="carbon:information" color="blue" v-if="dialogType === 'info'"></Icon>
                        <Icon icon="carbon:warning" color="orange" v-if="dialogType === 'warning'"></Icon>
                        <Icon icon="carbon:misuse" color="red" v-if="dialogType === 'error'"></Icon>
                    </div>
                    <i @click="hide()">
                        <Icon icon="carbon:close"></Icon>
                    </i>
                </header>
                <div class="message mt-1 has-text-size-larger" v-if="message"><: message :></div>
                <details v-if="!!dumpMessage">
                    <summary><: t('details') :></summary>
                    <pre class="dump"><: dumpMessage :></pre>
                </details>
                <input class="mt-1" type="text" v-if="dialogType === 'prompt'" v-model.lazy="inputValue" />
                <div class="mt-1" v-if="dialogType === 'prompt'" v-show="checkLabel">
                    <input type="checkbox" id="_check" v-model.lazy="checkValue"  />
                    <label for="_check"><: checkLabel :></label>
                </div>
                <div class="actions mt-2">
                    <button
                        class="button-outlined-white confirm mr-1"
                        :class="{'is-loading-spinner': loading }"
                        :disabled="loading === true"
                        @click="prepareCallback() && confirmCallback(inputValue, checkValue, $root)"
                        v-if="confirmMessage">
                        <: confirmMessage :>
                    </button>
                    <button
                        class="button-secondary cancel"
                        :disabled="loading === true"
                        @click="hide()"
                        v-if="cancelMessage">
                        <: cancelMessage :>
                    </button>
                </div>
            </div>
        </transition>
    </div>`,

    components: {
        Icon,
    },

    data() {
        return {
            destroyOnHide: true,
            isOpen: false,
            dialogType: 'warning',
            headerText: '',
            message: '',
            dumpMessage: false,
            confirmMessage: 'ok',
            confirmCallback: this.hide,
            cancelMessage: false,
            inputValue: '',
            checkValue: '',
            checkLabel: '',
            loading: false
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
            this.show(message, this.dialogType, root);
        },
        error(message, root = document.body, dumpMessage = false) {
            this.dialogType = 'error';
            this.dumpMessage = dumpMessage;
            this.show(message, this.dialogType, root);
        },
        info(message, root = document.body) {
            this.dialogType = 'info';
            this.show(message, this.dialogType, root);
        },
        success(message, root = document.body) {
            this.dialogType = 'success';
            this.show(message, this.dialogType, root);
        },
        confirm(message, confirmMessage, confirmCallback, type = 'warning', root = document.body) {
            this.dialogType = type;
            this.confirmMessage = confirmMessage;
            this.confirmCallback = confirmCallback;
            this.cancelMessage = t`cancel`;
            this.show(message, this.dialogType, root);
        },
        prompt(message, defaultValue, confirmCallback, root = document.body, options = {}) {
            this.dialogType = 'prompt';
            this.inputValue = defaultValue || '';
            this.checkValue = options?.checkValue || '';
            this.checkLabel = options?.checkLabel || '';
            this.confirmCallback = confirmCallback;
            this.cancelMessage = t`cancel`;
            this.show(message, '', root);
        },
        prepareCallback() {
            this.loading = true

            return true;
        },
    },
});

export const warning = (message, root) => {
    const dialog = new Dialog();
    dialog.warning(message, root);

    return dialog;
};

export const error = (message, root, dumpMessage = false) => {
    const dialog = new Dialog();
    dialog.error(message, root, dumpMessage);

    return dialog;
};

export const info = (message, root) => {
    const dialog = new Dialog();
    dialog.info(message, root);

    return dialog;
};

export const success = (message, root) => {
    const dialog = new Dialog();
    dialog.success(message, root);

    return dialog;
};

export const confirm = (message, confirmMessage, confirmCallback, type, root) => {
    const dialog = new Dialog();
    dialog.confirm(message, confirmMessage, confirmCallback, type, root);

    return dialog;
};

export const prompt = (message, defaultValue, confirmCallback, root, options) => {
    const dialog = new Dialog();
    dialog.prompt(message, defaultValue, confirmCallback, root, options);

    return dialog;
};
