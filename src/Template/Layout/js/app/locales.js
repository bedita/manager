import { addLocale, useLocale } from 'ttag';

/**
 * Setup locale using locale parameter
 */
export default function setupLocale(locale) {
    if (locale) {
        const translationObj = require(`./locales/${locale}.json`);
        addLocale(locale, translationObj);
        useLocale(locale);
    }
}
