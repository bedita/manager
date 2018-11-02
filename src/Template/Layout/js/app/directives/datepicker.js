/**
 * Datepicker vue directive
 *
 */

import flatpickr from 'flatpickr/dist/flatpickr.min';
import moment from 'moment';
import 'flatpickr/dist/flatpickr.min.css';


const dateTimePickerOptions = {
    enableTime: true,
    altInput: true,
    dateFormat: 'Z', // ISO8601 for db
    altFormat: 'D/M/Y HH:MM', // moment format / visible
    animate: false,
    time_24hr: true,
};

const datepickerOptions = {
    enableTime: false,
    altInput: true,
    dateFormat: 'Z', // ISO8601 for db
    altFormat: 'D/M/Y', // moment format / visible
    animate: false,
    time_24hr: true,
};

export default {
    install(Vue) {
        Vue.directive('datepicker', {
            /**
             * create flatpicker instance when element is inserted
             *
             * @param {Object} element DOM object
             */
            inserted (element, dir, vueEl) {
                let options = datepickerOptions;

                if (vueEl.data && vueEl.data.attrs && vueEl.data.attrs.time === 'true') {
                    options = dateTimePickerOptions;
                }

                // custom format Date so we can show the current timezone
                options.formatDate = (dateObj, format) => {
                    // this value goes to the hidden input which will be saved, needs to be a correct ISO8601
                    if (format === 'Z') {
                        return moment(dateObj).toISOString();
                    }
                    // or else timezone infos will be added to date string
                    let now = new Date();
                    let offset = now.getTimezoneOffset();
                    let tmz = offset / 60;
                    let sgn = tmz < 0 ? '+' : '-';
                    let formatUTC = `${format} (UTC${sgn}${Math.abs(tmz)})`;
                    return moment(dateObj).format(formatUTC);
                }

                try {
                    let datePicker = flatpickr(element, options);
                    element.dataset.originalValue = element.value;
                    // element._flatpickr = datePicker;

                    let clearButton = document.createElement('span');
                    clearButton.classList.add('clear-button');
                    clearButton.innerHTML = '&times;';
                    clearButton.addEventListener('click', () => {
                        datePicker.clear();
                    });

                    element.parentElement.appendChild(clearButton);
                } catch (err) {
                    console.error(err);
                }
            },
        })
    }
}
