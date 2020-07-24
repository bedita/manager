import Vue from 'vue';

const methods = {
    /**
    * autoTranslate text(s) from a language to another.
    *
    * @param {Array|String} text The text(s) to translate
    * @param {String} from The source language
    * @param {String} to The target language
    * @returns {JSON} The translation data, i.e.
    * {
    *     "translation": [
    *         "<translation of first text>",
    *         "<translation of second text>",
    *         [...]
    *         "<translation of last text>"
    *     ]
    * }
    */
    async autoTranslate(text, from, to) {
        if (!text) {
            return;
        }
        const formData = new FormData();
        formData.append('from', from);
        formData.append('to', to);
        formData.append('text', text);
        const options = {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'accept': 'application/json',
                'X-CSRF-Token': BEDITA.csrfToken,
            },
            body: formData,
        };

        let responseJson = {translation: []};
        try {
            const response = await fetch(`${window.location.origin}/translate`, options);
            responseJson = await response.json();
        } catch (error) {
            console.error(error);
        }

        return responseJson;
    },
}

export default {
    install (Vue, options) {
        Vue.prototype.$helpers = methods;
    }
};
