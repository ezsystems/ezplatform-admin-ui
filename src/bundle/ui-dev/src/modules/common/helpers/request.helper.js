/**
 * Returns basic RequestInit object for Request
 *
 * @function getBasicRequestInit
 * @param {Object} restInfo REST config hash containing: token and siteaccess properties
 * @returns {RequestInit}
 */
export const getBasicRequestInit = ({ token, siteaccess }) => {
    return {
        headers: {
            'X-Siteaccess': siteaccess,
            'X-CSRF-Token': token,
        },
        mode: 'same-origin',
        credentials: 'same-origin',
    };
};

/**
 * Handles request error
 *
 * @function handleRequestResponse
 * @param {Response} response
 * @returns {Response}
 */
export const handleRequestError = (response) => {
    if (!response.ok) {
        throw Error(response.statusText);
    }

    return response;
};

/**
 * Handles request response
 *
 * @function handleRequestResponse
 * @param {Response} response
 * @returns {Error|Promise}
 */
export const handleRequestResponse = (response) => {
    return handleRequestError(response).json();
};

/**
 * Handles request response; returns status if response is OK
 *
 * @function handleRequestResponseStatus
 * @param {Response} response
 * @returns {number}
 */
export const handleRequestResponseStatus = (response) => {
    return handleRequestError(response).status;
};
