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
        span.title = t`Open Uri in new tab`;
        span.classList.add('icon-website');
        span.style = 'cursor: pointer;';
        span.addEventListener('click', (ev) => {
            ev.preventDefault()
            ev.stopPropagation();
            if (this.el.value.length < 10) {
                return;
            }
            window.open(this.el.value, '_blank').focus();
        });
        this.el.parentElement.appendChild(span);
    },
};
