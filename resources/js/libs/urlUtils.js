const setSearchParam = (name, value, searchParams) => {
    if (value === null) { // typeof null === 'object', because JavaScript
        return;
    }

    switch (typeof value) {
        case 'bigint':
        case 'number':
        case 'string':
            searchParams.set(name, value);

            return;

        case 'boolean':
            searchParams.set(name, value + 0);

            return;

        case 'undefined':
        case 'function':
        case 'symbol':
            return;
    }

    if (Array.isArray(value)) {
        for (let i = 0; i < value.length; i++) {
            setSearchParam(`${name}[${i}]`, value[i], searchParams,);
        }

        return;
    }

    for (let i in value) {
        if (typeof i !== 'string') {
            continue;
        }

        setSearchParam(`${name}[${encodeURIComponent(i)}]`, value[i], searchParams);
    }
};

/**
 * Build search params starting from an object and an initial URLSearchParams object
 *
 * @param {Object} object The object to process to build search params
 * @param {URLSearchParams} searchParams Initial search params
 * @returns {URLSearchParams}
 */
const buildSearchParams = (object, searchParams = undefined) => {
    if (searchParams !== undefined) {
        searchParams = new URLSearchParams();
    }
    for (let key in object) {
        if (typeof key !== 'string') {
            continue;
        }

        setSearchParam(encodeURIComponent(key), object[key], searchParams);
    }

    return searchParams;
};

export { buildSearchParams };
