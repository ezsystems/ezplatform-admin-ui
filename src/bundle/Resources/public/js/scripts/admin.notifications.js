(function(global, doc, eZ, $) {
    const notificationsContainer = doc.querySelector('.ez-notifications-container');
    const notifications = JSON.parse(notificationsContainer.dataset.notifications);
    const template = notificationsContainer.dataset.template;
    const addNotification = ({ detail }) => {
        const { onShow, label, message, rawPlaceholdersMap = {} } = detail;
        // @TODO: Unify 'error' and 'danger' label in 3.0.
        const configKey = label === 'danger' ? 'error' : label;
        const config = eZ.adminUiConfig.notifications[configKey];
        const timeout = config ? config.timeout : 0;
        const container = doc.createElement('div');
        let finalMessage = eZ.helpers.text.escapeHTML(message);

        Object.entries(rawPlaceholdersMap).forEach(([placeholder, rawText]) => {
            finalMessage = finalMessage.replace(`{{ ${placeholder} }}`, rawText);
        });

        const notification = template.replace('{{ label }}', label).replace('{{ message }}', finalMessage);

        container.insertAdjacentHTML('beforeend', notification);

        const notificationNode = container.querySelector('.alert');

        notificationsContainer.append(notificationNode);

        if (timeout) {
            global.setTimeout(() => $(notificationNode).alert('close'), timeout);
        }

        if (typeof onShow === 'function') {
            onShow(notificationNode);
        }
    };

    Object.entries(notifications).forEach(([label, messages]) => {
        messages.forEach((message) => addNotification({ detail: { label, message } }));
    });

    doc.body.addEventListener('ez-notify', addNotification, false);
})(window, document, window.eZ, window.jQuery);
