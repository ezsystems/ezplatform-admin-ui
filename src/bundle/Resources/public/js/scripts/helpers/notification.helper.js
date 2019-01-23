(function(global, doc) {
    const eZ = (global.eZ = global.eZ || {});

    const NOTIFICATION_INFO_LABEL = 'info';
    const NOTIFICATION_SUCCESS_LABEL = 'success';
    const NOTIFICATION_WARNING_LABEL = 'warning';
    const NOTIFICATION_ERROR_LABEL = 'danger';

    /**
     * Dispatches notification event
     *
     * @function showNotification
     * @param {Object} detail
     * @param {String} detail.message
     * @param {String} detail.label
     * @param {Function} [detail.onShow] to be called after notification Node was added
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
     */
    const showInfoNotification = (message, onShow) =>
        showNotification({
            message,
            label: NOTIFICATION_INFO_LABEL,
            onShow,
        });

    /**
     * Dispatches success notification event
     *
     * @function showSuccessNotification
     * @param {String} message
     * @param {Function} [onShow] to be called after notification Node was added
     */
    const showSuccessNotification = (message, onShow) =>
        showNotification({
            message,
            label: NOTIFICATION_SUCCESS_LABEL,
            onShow,
        });

    /**
     * Dispatches warning notification event
     *
     * @function showWarningNotification
     * @param {String} message
     * @param {Function} [onShow] to be called after notification Node was added
     */
    const showWarningNotification = (message, onShow) =>
        showNotification({
            message,
            label: NOTIFICATION_WARNING_LABEL,
            onShow,
        });

    /**
     * Dispatches danger notification event
     *
     * @function showDangerNotification
     * @param {String} message
     */
    const showDangerNotification = (message) => {
        console.warn('[DEPRECATED] showDangerNotification is deprecated');
        console.warn('[DEPRECATED] it will be removed from ezplatform-admin-ui 2.0');
        console.warn('[DEPRECATED] use showErrorNotification instead');

        showErrorNotification(message);
    };

    /**
     * Dispatches error notification event
     *
     * @function showErrorNotification
     * @param {(string | Error)} error
     * @param {Function} [onShow] to be called after notification Node was added
     */
    const showErrorNotification = (error, onShow) => {
        const isErrorObj = error instanceof Error;
        const message = isErrorObj ? error.message : error;

        showNotification({
            message,
            label: NOTIFICATION_ERROR_LABEL,
            onShow,
        });
    };

    eZ.helpers = eZ.helpers || {};
    eZ.helpers.notification = {
        showNotification,
        showInfoNotification,
        showSuccessNotification,
        showWarningNotification,
        showDangerNotification,
        showErrorNotification,
    };
})(window, document);
