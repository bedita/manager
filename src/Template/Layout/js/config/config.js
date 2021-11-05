// Vue configs...

import Vue from 'vue';
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

// merge vue options and configs
for (let property in VueConfig) {
    if (Object.prototype.hasOwnProperty.call(VueConfig, property)) {
        Vue.config[property] = VueConfig[property];
    }
}

for (let property in VueOptions) {
    if (Object.prototype.hasOwnProperty.call(VueOptions, property)) {
        Vue.options[property] = VueOptions[property];
    }
}

Locale(BEDITA.locale);

// General Configs

// Media object types with possible file uploads
export const ACCEPTABLE_MEDIA = BEDITA.uploadable;

// Global mixins

Vue.mixin({
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
});
