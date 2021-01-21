import { t } from 'ttag';

/**
 * Component to display a dialog.
 * Find its instance by searching its ref, then set its data or call its methods:
 * e.g. `this.$root.$refs.beditaDialog.confirm('are you sure?', 'yes', () => console.log('confirmed!'))`
 */
export const BeditaDialog = {
    template: `<div class="bedita-dialog">
        <transition name="fade">
            <div class="backdrop" @click="hide()" v-if="isOpen"></div>
        </transition>
        <transition name="slide">
            <div class="dialog p-1 has-background-white has-text-black" :class="dialogType" v-if="isOpen">
                <header class="is-flex space-between align-center is-expanded">
                    <div class="is-flex align-center">
                        <span class="is-capitalized mr-05" v-if="headerText"><: headerText :></span>
                        <i :class="icon" class="has-text-size-larger" v-if="icon"></i>
                    </div>
                    <i class="icon-cancel-1 has-text-size-larger" @click="hide()"></i>
                </header>
                <div class="message mt-1 has-text-size-larger" v-if="message"><: message :></div>
                <input class="mt-1" type="text" v-if="dialogType == 'prompt'" v-model.lazy="inputValue" />
                <div class="actions mt-2">
                    <button class="button-outlined-white confirm mr-1" v-if="confirmMessage" @click="confirmCallback(inputValue);"><: confirmMessage :></button>
                    <button class="button-secondary cancel" @click="hide()" v-if="cancelMessage"><: cancelMessage :></button>
                </div>
            </div>
        </transition>
    </div>`,

    data() {
        return {
            isOpen: false,
            dialogType: 'warning',
            headerText: t(this.dialogType),
            icon: 'icon-attention-circled',
            message: '',
            confirmMessage: 'ok',
            confirmCallback: this.hide,
            cancelMessage: t`cancel`,
            inputValue: '',
        };
    },

    methods: {
        show(message) {
            this.message = message;
            this.isOpen = true;
        },
        hide() {
            this.dialogType = '';
            this.isOpen = false;
        },
        warning(message) {
            this.dialogType = 'warning';
            this.show(message);
        },
        error(message) {
            this.dialogType = 'error';
            this.show(message);
        },
        info(message) {
            this.dialogType = 'info';
            this.icon = 'icon-info-1';
            this.show(message);
        },
        confirm(message, confirmMessage, confirmCallback, type = 'warning') {
            this.dialogType = type;
            this.confirmMessage = confirmMessage;
            this.confirmCallback = confirmCallback;
            this.show(message);
        },
        prompt(message, defaultValue, confirmCallback) {
            this.dialogType = 'prompt';
            this.headerText = '';
            this.icon = 'icon-info-1';
            this.inputValue = defaultValue || '';
            this.confirmCallback = confirmCallback;
            this.show(message);
        },
    },
};
