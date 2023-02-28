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
