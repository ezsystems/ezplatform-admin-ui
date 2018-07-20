(function(global, doc) {
    const eZ = (global.eZ = global.eZ || {});

    /**
     * Handles request error
     *
     * @function handleRequestError
     * @param {Response} response
     * @returns {Error|Promise}
     */
    const handleRequestError = (response) => {
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response;
    };

    /**
     * Handles request JSON response
     *
     * @function handleRequestResponseJson
     * @param {Response} response
     * @returns {Error|Promise}
     */
    const handleRequestResponseJson = (response) => {
        return handleRequestError(response).json();
    };

    /**
     * Handles request text response
     *
     * @function handleRequestResponseText
     * @param {Response} response
     * @returns {Error|Promise}
     */
    const handleRequestResponseText = (response) => {
        return handleRequestError(response).text();
    };

    /**
     * Handles request response; returns status if response is OK
     *
     * @function handleRequestResponseStatus
     * @param {Response} response
     * @returns {Error|Promise}
     */
    const handleRequestResponseStatus = (response) => {
        return handleRequestError(response).status;
    };

    eZ.helpers = eZ.helpers || {};
    eZ.helpers.request = {
        handleRequestResponseJson,
        handleRequestResponseText,
        handleRequestResponseStatus,
    };
})(window, document);
