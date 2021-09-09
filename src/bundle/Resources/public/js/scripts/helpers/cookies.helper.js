(function (global, doc, eZ) {
    const setCookie = (name, value, maxAgeDays = 356, path = '/') => {
        const maxAge = maxAgeDays * 24 * 60 * 60;

        doc.cookie = `${name}=${value};max-age=${maxAge};path=${path}`;
    };
    const getCookie = (name) => {
        const cookieName = name + '=';
        const decodedCookie = decodeURIComponent(doc.cookie);
        const cookiesArray = decodedCookie.split(';');

        for (index in cookiesArray) {
            const cookieString = cookiesArray[index].trim();

            if (cookieString.indexOf(cookieName) === 0) {
                return cookieString.split('=')[1];
            }
        }

        return '';
    };

    eZ.addConfig('helpers.cookies', {
        getCookie,
        setCookie,
    });
})(window, window.document, window.eZ);
