import { t } from 'ttag';

export default {
    template: /* template */`
    <div>
        <slot></slot>
    </div>
    `,

    props: {
        el: {
            type: HTMLInputElement,
        },
        isValid: false
    },

    async mounted() {
        if (this.el.value === 'null') {
            this.el.value = '';
            this.isValid = true;
        }
        const span = document.createElement('span');
        span.title = t`Mail to`;
        span.classList.add('icon-mail-1');
        span.style = 'cursor: pointer;';
        span.addEventListener('click', (ev) => {
            ev.preventDefault()
            ev.stopPropagation();
            if (this.el.value.length < 8) {
                return;
            }
            window.open(`mailto:${this.el.value}`);
        });
        this.el.parentElement.appendChild(span);
    },
};
