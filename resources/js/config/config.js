// Vue configs...

import Locale from 'app/locales';
import { t } from 'ttag';

export const VueConfig = {
    devtools: true,
}

export const VueOptions = {
    delimiters: ['<:', ':>'],
}

// Polyfill
import 'abortcontroller-polyfill/dist/abortcontroller-polyfill-only';

Locale(BEDITA.locale);

// General Configs

// Media object types with possible file uploads
export const ACCEPTABLE_MEDIA = BEDITA.uploadable;

// Global mixins

export const GlobalMixin = {
    methods: {
        /**
         * ttag helper method for string literal template
         *
         * @param {string} value string to translate
         *
         * @return {string} translated value
          */
        t: (value) => {
            // call ttag t method
            return t([value]);
        },

        /**
         * Capitalize a string.
         *
         * @param {String} str The string to capitalize
         *
         * @return {string}
         */
        capitalize: (str) => {
            if (!str) {
                return '';
            }

            str = str.toString();
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
    }
};
