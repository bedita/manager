// flatpickr

/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <staggered-list> component used for lists with staggered animation
 *
 */

const datepickerOptions = {
    enableTime: false,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "F j, Y - H:i",
};

Vue.directive('datepicker', {
    /**
     * create flatpicker instance when element is inserted
     *
     * @param {Object} element DOM object
     */
    inserted (element, dir, vueEl) {
        let options = datepickerOptions;

        if (vueEl.data.attrs.time) {
            options.enableTime = vueEl.data.attrs.time;
        }

        let datePicker = flatpickr(element, options);

        let clearButton = document.createElement('span');
        clearButton.classList.add('clear-button');
        clearButton.innerHTML = '&times;';
        clearButton.addEventListener('click', () => {
            datePicker.clear();
        });

        element.parentElement.appendChild(clearButton);
    },
});
