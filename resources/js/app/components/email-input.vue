<template>
    <div>
        <slot />
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'EmailInput',

    props: {
        el: {
            type: HTMLInputElement,
            default: null,
        },
        isValid: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            element: this.el,
            valid: this.isValid,
        };
    },

    async mounted() {
        if (this.element.value === 'null') {
            this.element.value = '';
            this.valid = true;
        }
        const span = document.createElement('span');
        span.title = t`Mail to`;
        span.classList.add('icon-mail-1');
        span.style = 'cursor: pointer;';
        span.addEventListener('click', (ev) => {
            ev.preventDefault()
            ev.stopPropagation();
            if (this.element.value.length < 8) {
                return;
            }
            window.open(`mailto:${this.element.value}`);
        });
        this.element.parentElement.appendChild(span);
    },
};
</script>
