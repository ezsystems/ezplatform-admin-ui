(function(global, doc, eZ) {
    const NOTIFICATION_INFO_LABEL = 'info';
    const NOTIFICATION_SUCCESS_LABEL = 'success';
    const NOTIFICATION_WARNING_LABEL = 'warning';
    const NOTIFICATION_ERROR_LABEL = 'error';

    /**
     * Dispatches notification event
     *
     * @function showNotification
     * @param {Object} detail
     * @param {String} detail.message
     * @param {String} detail.label
     * @param {Function} [detail.onShow] to be called after notification Node was added
     * @param {Object} detail.rawPlaceholdersMap
     */
    const showNotification = (detail) => {
        const event = new CustomEvent('ez-notify', { detail });

        doc.body.dispatchEvent(event);
    };

    /**
     * Dispatches info notification event
     *
     * @function showInfoNotification
     * @param {String} message
     * @param {Function} [onShow] to be called after notification Node was added
     * @param {Object} rawPlaceholdersMap
     */
    const showInfoNotification = (message, onShow, rawPlaceholdersMap = {}) =>
        showNotification({
            message,
            label: NOTIFICATION_INFO_LABEL,
            onShow,
            rawPlaceholdersMap,
        });

    /**
     * Dispatches success notification event
     *
     * @function showSuccessNotification
     * @param {String} message
     * @param {Function} [onShow] to be called after notification Node was added
     * @param {Object} rawPlaceholdersMap
     */
    const showSuccessNotification = (message, onShow, rawPlaceholdersMap = {}) =>
        showNotification({
            message,
            label: NOTIFICATION_SUCCESS_LABEL,
            onShow,
            rawPlaceholdersMap,
        });

    /**
     * Dispatches warning notification event
     *
     * @function showWarningNotification
     * @param {String} message
     * @param {Function} [onShow] to be called after notification Node was added
     * @param {Object} rawPlaceholdersMap
     */
    const showWarningNotification = (message, onShow, rawPlaceholdersMap = {}) =>
        showNotification({
            message,
            label: NOTIFICATION_WARNING_LABEL,
            onShow,
            rawPlaceholdersMap,
        });

    /**
     * Dispatches error notification event
     *
     * @function showErrorNotification
     * @param {(string | Error)} error
     * @param {Function} [onShow] to be called after notification Node was added
     * @param {Object} rawPlaceholdersMap
     */
    const showErrorNotification = (error, onShow, rawPlaceholdersMap = {}) => {
        const isErrorObj = error instanceof Error;
        const message = isErrorObj ? error.message : error;

        showNotification({
            message,
            label: NOTIFICATION_ERROR_LABEL,
            onShow,
            rawPlaceholdersMap,
        });
    };

    eZ.addConfig('helpers.notification', {
        showNotification,
        showInfoNotification,
        showSuccessNotification,
        showWarningNotification,
        showErrorNotification,
    });
})(window, window.document, window.eZ);
