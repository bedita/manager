import { addLocale, useLocale } from 'ttag';

/**
 * Setup locale using locale parameter
 */
export default function setupLocale(locale) {
    if (locale) {
        // Locale is the webpack alias that points to locales path
        const translationObj = require(`Locale/${locale}/default.po`);
        addLocale(locale, translationObj);
        useLocale(locale);
    }
}
