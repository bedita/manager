const po = require('gettext-parser').po;

/**
 * @see https://github.com/cah4a/po-gettext-loader
 * Like the project above,  but with updated dependencies. //
 */
module.exports = function(source) {
    this.cacheable();
    return JSON.stringify(po.parse(source, 'utf-8'));
};
