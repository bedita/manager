<template>
    <div>
        <slot />
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'UriInput',
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
        const anchor = document.createElement('a');
        anchor.innerHTML = t`Open Uri`;
        anchor.classList.add('icon-link-ext');
        anchor.classList.add('mt-075');
        anchor.classList.add('is-block');
        anchor.style = 'cursor: pointer;';
        anchor.addEventListener('click', (ev) => {
            ev.preventDefault()
            ev.stopPropagation();
            if (this.element.value.length < 10) {
                return;
            }
            window.open(this.element.value, '_blank').focus();
        });
        this.element.parentElement.appendChild(anchor);
        const span = document.createElement('span');
        span.id = `valid_${this.element.id}`;
        span.style = 'padding-left: 10px';
        this.element.parentElement.appendChild(span);
        this.element.parentElement.classList.add('uri');
        this.element.onchange = this.onChange.bind(this);
    },

    methods: {
        onChange(ev) {
            ev.preventDefault()
            ev.stopPropagation();
            const span = document.getElementById(`valid_${this.element.id}`);
            span.classList.remove('icon-check-1');
            span.classList.remove('icon-error');
            this.element.value = this.element.value.trim();
            if (this.element.value.length === 0) {
                return;
            }
            if (!this.element.value.startsWith('http')) {
                this.element.value = `https://${this.element.value}`;
            }
            this.valid = this.isURLValid(this.element.value);
            if (!this.valid) {
                this.element.value = '';
            }
            span.classList.add(this.valid ? 'icon-check-1' : 'icon-error');
            const message = this.valid ? t`Uri is valid` : t`Uri is not valid`;
            span.innerHTML = message;
        },

        isURLValid(url) {
            try {
                const parsed = new URL(url);
                if (!['http:', 'https:'].includes(parsed.protocol)) {
                    return false;
                }

                // hostname must have at least one dot and a TLD of 2+ letters (no length upper bound, e.g. ".investments")
                // or a punycode TLD (IDN hostnames are converted by URL, e.g. ".рф" => ".xn--p1ai")
                return /^([a-z0-9-]+\.)+([a-z]{2,}|xn--[a-z0-9-]+)$/i.test(parsed.hostname);
            } catch {
                return false;
            }
        },
    }
};
</script>
