(function(doc) {
    const notificationsContainer = doc.querySelector('.ez-notifications-container');
    const notifications = JSON.parse(notificationsContainer.dataset.notifications);
    const template = notificationsContainer.dataset.template;

    const escapeHTML = (string) => {
        const stringTempNode = doc.createElement('div');

        stringTempNode.appendChild(doc.createTextNode(string));

        return stringTempNode.innerHTML;
    };

    const addNotification = ({ detail }) => {
        const { label, message } = detail;
        const templateLabel = label === 'error' ? 'danger' : label;
        const container = doc.createElement('div');
        let finalMessage = escapeHTML(message);

        const notification = template
            .replace('{{ label }}', templateLabel)
            .replace('{{ message }}', finalMessage)
            .replace('{{ badge }}', label);

        container.insertAdjacentHTML('beforeend', notification);

        const notificationNode = container.querySelector('.alert');

        notificationsContainer.append(notificationNode);
    };

    Object.entries(notifications).forEach(([label, messages]) => {
        messages.forEach((message) => addNotification({ detail: { label, message } }));
    });
})(window.document);
