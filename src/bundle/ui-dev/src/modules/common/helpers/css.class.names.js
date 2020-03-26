export const createCssClassNames = (classes) => {
    if (Object.prototype.toString.call(classes) !== '[object Object]') {
        return '';
    }

    return Object.entries(classes)
        .reduce((total, [name, condition]) => {
            return `${total} ${condition ? name : ''}`;
        }, '')
        .trim();
};
