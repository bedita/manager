import { t } from 'ttag';

/**
 * Component to display a dialog.
 * Find its instance by searching its ref, then set its data or call its methods:
 * e.g. `this.$root.$refs.beditaDialog.warning('are you sure?')`
 * e.g. `this.$root.$refs.beditaDialog.confirmCallback = () => console.log('confirmed!')`
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
                        <span class="is-capitalized"><: t(dialogType) :></span>
                        <i class="icon-attention-circled ml-05 has-text-size-larger"></i>
                    </div>
                    <i class="icon-cancel-1 has-text-size-larger" @click="hide()"></i>
                </header>
                <div class="message my-2 has-text-size-larger" v-if="message"><: message :></div>
                <div class="actions">
                    <button class="button-outlined-white confirm mr-1" v-if="confirmMessage" @click="confirmCallback()"><: confirmMessage :></button>
                    <button class="button-secondary cancel" @click="hide()" v-if="cancelMessage"><: cancelMessage :></button>
                </div>
            </div>
        </transition>
    </div>`,

    data() {
        return {
            isOpen: false,
            dialogType: 'warning',
            message: '',
            confirmMessage: '',
            confirmCallback: this.hide,
            cancelMessage: t`cancel`,
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
    },
};
