import 'flatpickr/dist/flatpickr.min.css';
import 'flatpickr/dist/themes/dark.css';

import flatpickr from 'flatpickr';
import moment from 'moment';

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

// date / time format (moment)
const DATE_FORMAT = 'D/M/Y';
const TIME_FORMAT = 'D/M/Y HH:mm';

// default flatpickr options
const FLATPICKR_OPTIONS = {
    enableTime: false,
    altInput: true,
    altFormat: DATE_FORMAT,
    dateFormat: 'Z', // ISO8601 for db
    animate: false,
    time_24hr: true,
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
        setupDatepicker(date) {
            // set locale
            if (LOCALE !== 'en') {
                moment.locale(LOCALE);
                flatpickr.localize(LOCALES_AVAILABLE[LOCALE]);
            }

            let element = this.el;
            let options = Object.assign({}, FLATPICKR_OPTIONS);

            if (this.attrs.time) {
                options.enableTime = true;
                options.altFormat = TIME_FORMAT;
            }

            // custom format Date so we can show the current timezone
            options.formatDate = (dateObj, format) => {
                // this value goes to the hidden input which will be saved, needs to be a correct ISO8601
                if (format === 'Z') {
                    return moment(dateObj, moment.ISO_8601).format('YYYY-MM-DDTHH:mm:ssZ');
                }
                // or else timezone infos will be added to date string
                let now = new Date();
                let offset = now.getTimezoneOffset();
                let tmz = offset / 60;
                let sgn = tmz <= 0 ? '+' : '-';
                let formatUTC = `${format} (UTC${sgn}${Math.abs(tmz)})`;
                return moment(dateObj).format(formatUTC);
            }

            try {
                let datePicker = this.instance = flatpickr(element, options);
                element.dataset.originalValue = element.value;
            } catch (err) {
                console.error(err);
            }
        },
    },
};
