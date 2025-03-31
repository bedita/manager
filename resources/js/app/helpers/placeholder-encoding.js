/**
 * Encode placeholder params to base64 using text encoder to ensure that the string is in utf-8 format
 * @param {string|undefined} value
 * @returns {string} base64 encoded string
 */
export function placeholderParamsEncode(value = 'undefined') {
    const utf8Bytes = new TextEncoder().encode(value);

    return btoa(String.fromCharCode(...utf8Bytes));
};

/**
 * Decode placeholder params from base64 to utf-8
 * @param {string| undefined} value
 * @returns {string} decoded string
 */
export function placeholderParamsDecoded(value) {
    const binaryString = atob(value);
    const bytes = Uint8Array.from(binaryString, c => c.charCodeAt(0));

    return new TextDecoder().decode(bytes);
};
