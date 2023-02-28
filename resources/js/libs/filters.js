import Vue from 'vue';
import { humanizeString } from '../app/helpers/text-helper.js';

/**
 * Converts a snake case string to title case.
 * Example: snake_case => Snake Case
 *
 * @param {String} str the string to convert
 * @return {String}
 */
Vue.filter('humanize', function(str) {
    return humanizeString(str);
});

/**
 * Capitalize a string.
 *
 * @param {String} str The string to capitalize
 */
Vue.filter('capitalize', function(str) {
    if (!str) {
        return '';
    }

    str = str.toString();
    return str.charAt(0).toUpperCase() + str.slice(1);
});
