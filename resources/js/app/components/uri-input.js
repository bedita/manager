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
        anchor.innerHTML = t`Open Uri`;
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
        const span = document.createElement('span');
        span.id = `valid_${this.el.id}`;
        span.style = 'padding-left: 10px';
        this.el.parentElement.appendChild(span);
        this.el.onchange = this.onChange.bind(this);
    },

    methods: {
        onChange(ev) {
            ev.preventDefault()
            ev.stopPropagation();
            const span = document.getElementById(`valid_${this.el.id}`);
            span.classList.remove('icon-check-1');
            span.classList.remove('icon-error');
            this.el.value = this.el.value.trim();
            if (this.el.value.length === 0) {
                return;
            }
            if (!this.el.value.startsWith('http')) {
                this.el.value = `https://${this.el.value}`;
            }
            this.isValid = this.isURLValid(this.el.value);
            if (!this.isValid) {
                this.el.value = '';
            }
            span.classList.add(this.isValid ? 'icon-check-1' : 'icon-error');
            const message = this.isValid ? t`Uri is valid` : t`Uri is not valid`;
            span.innerHTML = message;
        },

        isURLValid(url) {
            const regex = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi;

            return regex.test(url);
        }
    }
};
