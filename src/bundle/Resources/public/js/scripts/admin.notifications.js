(function (global, doc, eZ, $) {
    const notificationsContainer = doc.querySelector('.ez-notifications-container');
    const notifications = JSON.parse(notificationsContainer.dataset.notifications);
    const template = notificationsContainer.dataset.template;
    const addNotification = ({ detail }) => {
        // @TODO: Unify 'error' and 'danger' label in 3.0.
        const configKey = detail.label === 'danger' ? 'error' : detail.label;
        const config = eZ.adminUiConfig.notifications[configKey];
        const timeout = config ? config.timeout : 0;
        const container = doc.createElement('div');
        const notification = template.replace('{{ label }}', detail.label).replace('{{ message }}', detail.message);

        container.insertAdjacentHTML('beforeend', notification);

        const notificationNode = container.querySelector('.alert');

        notificationsContainer.append(notificationNode);

        if (timeout) {
            global.setTimeout(() => $(notificationNode).alert('close'), timeout);
        }
    };

    Object.entries(notifications).forEach(([ label, messages ]) => {
        messages.forEach(message => addNotification({ detail: { label, message }}));
    });

    doc.body.addEventListener('ez-notify', addNotification, false);
})(window, window.document, window.eZ, window.jQuery);
