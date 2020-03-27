/**
 * Clones any object. Faster alternative to `JSON.parse(JSON.stringify)`
 *
 * @function deepClone
 * @param {Any} data
 * @returns {Any} cloned data
 */
const deepClone = (data) => {
    let clonedData;

    if (typeof data !== 'object') {
        return data;
    }

    if (!data) {
        return data;
    }

    if (Object.prototype.toString.apply(data) === '[object Array]') {
        clonedData = [];

        for (let i = 0; i < data.length; i++) {
            clonedData[i] = deepClone(data[i]);
        }

        return clonedData;
    }

    clonedData = {};

    for (let i in data) {
        if (data.hasOwnProperty(i)) {
            clonedData[i] = deepClone(data[i]);
        }
    }

    return clonedData;
};

export default deepClone;
