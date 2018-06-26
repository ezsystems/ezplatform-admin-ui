(function(global, doc) {
    const eZ = (global.eZ = global.eZ || {});

    const NOTIFICATION_INFO_LABEL = 'info';
    const NOTIFICATION_SUCCESS_LABEL = 'success';
    const NOTIFICATION_WARNING_LABEL = 'warning';
    const NOTIFICATION_DANGER_LABEL = 'danger';

    /**
     * Dispatches notification event
     *
     * @function showNotification
     * @param {Object} detail
     */
    const showNotification = (detail) => {
        const event = new CustomEvent('ez-notify', {
            detail,
        });

        document.body.dispatchEvent(event);
    };

    /**
     * Dispatches info notification event
     *
     * @function showInfoNotification
     * @param {String} message
     */
    const showInfoNotification = (message) => {
        showNotification({
            message,
            label: NOTIFICATION_INFO_LABEL,
        });
    };

    /**
     * Dispatches success notification event
     *
     * @function showSuccessNotification
     * @param {String} message
     */
    const showSuccessNotification = (message) => {
        showNotification({
            message,
            label: NOTIFICATION_SUCCESS_LABEL,
        });
    };

    /**
     * Dispatches warning notification event
     *
     * @function showWarningNotification
     * @param {String} message
     */
    const showWarningNotification = (message) => {
        showNotification({
            message,
            label: NOTIFICATION_WARNING_LABEL,
        });
    };

    /**
     * Dispatches danger notification event
     *
     * @function showDangerNotification
     * @param {String} message
     */
    const showDangerNotification = (message) => {
        showNotification({
            message,
            label: NOTIFICATION_DANGER_LABEL,
        });
    };

    /**
     * Dispatches danger notification event
     *
     * @function showErrorNotification
     * @param {Error} error
     */
    const showErrorNotification = (error) => {
        showDangerNotification(error.message);
    };

    eZ.services = eZ.services || {};
    eZ.services.notification = {
        showInfoNotification,
        showSuccessNotification,
        showWarningNotification,
        showDangerNotification,
        showErrorNotification,
    };
})(window, document);
