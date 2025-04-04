export function humanizeString(string) {
    if (!string) {
        return '';
    }
    string = string
        .toLowerCase()
        .replace(/[_-]+/g, ' ')
        .replace(/\s{2,}/g, ' ')
        .trim();
    const words = string.split(' ');
    let res = '';
    words.forEach((entry) => {
        res += ' ' + upperCaseFirst(entry);
    });

    return res.trim();
}

export function upperCaseFirst(input) {
    return input.charAt(0).toUpperCase() + input.substr(1);
}


/**
 * Encode string to base64 using text encoder to ensure that the string is in utf-8 format
 * @param {string|undefined} value
 * @returns {string} base64 encoded string
 */
export function utf8ToBase64(value = 'undefined') {
    const utf8Bytes = new TextEncoder().encode(value);

    return btoa(String.fromCharCode(...utf8Bytes));
};

/**
 * Decode string from base64 to utf-8
 * @param {string| undefined} value
 * @returns {string} decoded string
 */
export function base64ToUtf8(value) {
    const binaryString = atob(value);
    const bytes = Uint8Array.from(binaryString, c => c.charCodeAt(0));

    return new TextDecoder().decode(bytes);
};
