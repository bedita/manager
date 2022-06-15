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
        const anchor = document.createElement('a');
        anchor.innerHTML = t`Open Uri in new tab`;
        anchor.classList.add('icon-link-ext');
        anchor.style = 'cursor: pointer;';
        anchor.addEventListener('click', (ev) => {
            ev.preventDefault()
            ev.stopPropagation();
            if (this.el.value.length < 10) {
                return;
            }
            window.open(this.el.value, '_blank').focus();
        });
        this.el.parentElement.appendChild(anchor);
    },
};
