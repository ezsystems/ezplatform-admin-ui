(function(global, doc, eZ, bootstrap) {
    const notificationsContainer = doc.querySelector('.ez-notifications-container');
    const notifications = JSON.parse(notificationsContainer.dataset.notifications);
    const template = notificationsContainer.dataset.template;
    const addNotification = ({ detail }) => {
        const { onShow, label, message, rawPlaceholdersMap = {} } = detail;
        const templateLabel = label === 'error' ? 'danger' : label;
        const config = eZ.adminUiConfig.notifications[label];
        const timeout = config ? config.timeout : 0;
        const container = doc.createElement('div');
        let finalMessage = eZ.helpers.text.escapeHTML(message);

        Object.entries(rawPlaceholdersMap).forEach(([placeholder, rawText]) => {
            finalMessage = finalMessage.replace(`{{ ${placeholder} }}`, rawText);
        });

        const notification = template
            .replace('{{ label }}', templateLabel)
            .replace('{{ message }}', finalMessage)
            .replace('{{ badge }}', label);

        container.insertAdjacentHTML('beforeend', notification);

        const notificationNode = container.querySelector('.alert');

        notificationsContainer.append(notificationNode);

        if (timeout) {
            global.setTimeout(() => notificationNode.querySelector('.close').click(), timeout);
        }

        if (typeof onShow === 'function') {
            onShow(notificationNode);
        }
    };

    Object.entries(notifications).forEach(([label, messages]) => {
        messages.forEach((message) => addNotification({ detail: { label, message } }));
    });

    doc.body.addEventListener('ez-notify', addNotification, false);
})(window, window.document, window.eZ, window.bootstrap);
