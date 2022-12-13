import Vue from 'vue';

/**
 * Converts a snake case string to title case.
 * Example: snake_case => Snake Case
 *
 * @param {String} str the string to convert
 * @return {String}
 */
Vue.filter('humanize', function(str) {
    return str.split('_').map(function(item) {
        return item.charAt(0).toUpperCase() + item.substring(1);
    }).join(' ');
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
