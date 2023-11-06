const methods = {
    /**
    * autoTranslate text(s) from a language to another.
    *
    * @param {Array|String} text The text(s) to translate
    * @param {String} from The source language
    * @param {String} to The target language
    * @param {String} translator The translation engine
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
    autoTranslate(text, from, to, translator) {
        if (!text) {
            return;
        }
        if (from === 'en') {
            from = 'en-US';
        }
        if (to === 'en') {
            to = 'en-US';
        }
        const formData = new FormData();
        formData.append('from', from);
        formData.append('to', to);
        formData.append('translator', translator);
        if (Array.isArray(text)) {
            for (let t of text) {
                formData.append('text[]', t);
            }
        } else {
            formData.append('text', text);
        }
        const options = {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'accept': 'application/json',
                'X-CSRF-Token': BEDITA.csrfToken,
            },
            body: formData,
        };
        return fetch(`${window.location.origin}/translate`, options)
            .catch(error => {
                console.error(error);
                return error;
            })
            .then(r => r.json());
    },
}

export default {
    install (Vue) {
        Vue.prototype.$helpers.autoTranslate = methods.autoTranslate;
    }
};
