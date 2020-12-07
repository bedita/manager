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
    if (VueConfig.hasOwnProperty(property)) {
        Vue.config[property] = VueConfig[property];
    }
}

for (let property in VueOptions) {
    if (VueOptions.hasOwnProperty(property)) {
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
    }
});
