import 'flatpickr/dist/flatpickr.min.css';
import 'flatpickr/dist/themes/dark.css';
import flatpickr from 'flatpickr';
// fixed import for locales
import { Italian } from 'flatpickr/dist/l10n/it.js';
import { French } from 'flatpickr/dist/l10n/fr.js';
import { German } from 'flatpickr/dist/l10n/de.js';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import { Catalan } from 'flatpickr/dist/l10n/cat.js';
import { Portuguese } from 'flatpickr/dist/l10n/pt.js';

const LOCALES_AVAILABLE = {
    it: Italian,
    fr: French,
    de: German,
    es: Spanish,
    cat: Catalan,
    pt: Portuguese,
}
// default flatpickr options
const FLATPICKR_OPTIONS = {
    enableTime: false,
    altInput: true,
    dateFormat: 'Z', // ISO8601
    animate: false,
    time_24hr: true,
    enableSeconds: false,
};
// get loaded locale
const LOCALE = BEDITA.locale.slice(0, 2);
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
        attrs: {
            type: Object,
            required: true,
        },
    },
    watch: {
        attrs: function(val) {
            if (val.time != this.instance.config.enableTime) {
                // flatpickr doesn't allow dynamic config change. we must destroy and re-create it.
                /// see https://github.com/flatpickr/flatpickr/issues/1546
                const date = this.instance.latestSelectedDateObj;
                this.instance.destroy();
                this.setupDatepicker();
                this.setDate(date);
            }
        }
    },
    mounted() {
        this.setupDatepicker();
        let clearButton = document.createElement('span');
        clearButton.classList.add('clear-button');
        clearButton.innerHTML = '&times;';
        clearButton.addEventListener('click', (ev) => {
            ev.preventDefault()
            ev.stopPropagation();
            this.instance.clear();
        });
        this.el.parentElement.appendChild(clearButton);
    },
    destroyed() {
        if (this.instance) {
            this.instance.destroy();
            delete this.instance;
        }
    },
    methods: {
        setDate(date) {
            if (!this.instance) {
                return;
            }
            if (date) {
                this.instance.setDate(date);
            } else {
                this.instance.clear();
            }
        },
        setupDatepicker() {
            // set locale
            flatpickr.localize(LOCALES_AVAILABLE[LOCALE] || 'en');
            let element = this.el;
            let options = Object.assign({}, FLATPICKR_OPTIONS);
            let dateFormatOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            };

            if (this.attrs.time) {
                options.enableTime = true;
                Object.assign(dateFormatOptions, {
                    hour: 'numeric',
                    minute: 'numeric',
                });
            } else if (!this.attrs.daterange) {
                options.dateFormat = 'Y-m-d';
            }

            options.formatDate = (dateObj, format) => {
                // this value goes to the hidden input which will be saved,
                // needs to be a correct ISO 8601 string if a datetime, or a string
                // in "YYYY-MM-DD" format in case of simple date
                if (!element.attributes.time && !element.attributes.daterange) {
                    const date = new Date(dateObj.getTime());
                    // force hours to 12 to avoid date change
                    date.setHours(12);

                    return flatpickr.formatDate(date, format);
                }

                if (format === 'Z') {
                    return dateObj.toISOString();
                }

                return Intl.DateTimeFormat(LOCALE, dateFormatOptions).format(dateObj);
            };

            this.instance = flatpickr(element, options);
            element.dataset.originalValue = element.value;
        },
    },
};
