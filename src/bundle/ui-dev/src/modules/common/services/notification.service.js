export const NOTIFICATION_INFO_LABEL = 'info';
export const NOTIFICATION_SUCCESS_LABEL = 'success';
export const NOTIFICATION_WARNING_LABEL = 'warning';
export const NOTIFICATION_ERROR_LABEL = 'error';

/**
 * Dispatches notification event
 *
 * @method showNotification
 * @param {{message: string, label: string}} detail
 */
export const showNotification = (detail) => {
    const event = new CustomEvent('ez-notify', {
        detail,
    });

    document.body.dispatchEvent(event);
};

/**
 * Dispatches info notification event
 *
 * @method showInfoNotification
 * @param {String} message
 */
export const showInfoNotification = (message) => {
    showNotification({
        message,
        label: NOTIFICATION_INFO_LABEL,
    });
};

/**
 * Dispatches success notification event
 *
 * @method showSuccessNotification
 * @param {String} message
 */
export const showSuccessNotification = (message) => {
    showNotification({
        message,
        label: NOTIFICATION_SUCCESS_LABEL,
    });
};

/**
 * Dispatches warning notification event
 *
 * @method showWarningNotification
 * @param {String} message
 */
export const showWarningNotification = (message) => {
    showNotification({
        message,
        label: NOTIFICATION_WARNING_LABEL,
    });
};

/**
 * Dispatches error notification event
 *
 * @method showErrorNotification
 * @param {(string|Error)} error
 */
export const showErrorNotification = (error) => {
    const isErrorObj = error instanceof Error;
    const message = isErrorObj ? error.message : error;

    showNotification({
        message,
        label: NOTIFICATION_ERROR_LABEL,
    });
};
