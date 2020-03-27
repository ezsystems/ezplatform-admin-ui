/**
 * Returns a filesize as a formatted string
 *
 * @function fileSizeToString
 * @param {Number} filesize
 * @returns {String}
 */
export const fileSizeToString = (filesize) => {
    const units = ['bytes', 'KB', 'MB', 'GB'];
    const kilobyte = 1024;
    let size = parseInt(filesize, 10) || 0;
    let unitIndex = 0;
    let decimalUnits;

    while (size >= kilobyte) {
        size = size / kilobyte;
        unitIndex++;
    }

    decimalUnits = unitIndex < 1 ? 0 : 1;

    return size.toFixed(size >= 10 || decimalUnits) + ' ' + units[unitIndex];
};
