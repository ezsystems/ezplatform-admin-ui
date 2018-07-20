(function(global, doc, eZ, React, ReactDOM, Translator) {
    let currentPageLink = null;
    const SELECTOR_MODAL_ITEM = '.n-notifications-modal__item';
    const SELECTOR_MODAL_SPINNER = '.n-notifications-modal__spinner';
    const SELECTOR_MODAL_RESULTS = '.n-notifications-modal__results';
    const SELECTOR_DESC_TEXT = '.description__text';
    const SELECTOR_TABLE = '.n-table--notifications';
    const CLASS_ELLIPSIS = 'description__text--ellipsis';
    const CLASS_PAGINATION_LINK = 'page-link';
    const CLASS_MODAL_SPINNER_INVISIBLE = 'n-notifications-modal__spinner--invisible';
    const CLASS_MODAL_RESULTS_INVISIBLE = 'n-notifications-modal__results--invisible';
    const INTERVAL = 30000;
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const modal = doc.querySelector('.n-notifications-modal');
    const handleResponseError = eZ.helpers.notification.showErrorNotification;
    const handleRequestResponseJson = eZ.helpers.request.handleRequestResponseJson;
    const handleRequestResponseText = eZ.helpers.request.handleRequestResponseText;
    const onNotificationMarkedAsRead = (notification, response) => {
        if (response.status === 'success') {
            notification.classList.add('n-notifications-modal__item--read');
        }

        if (response.redirect) {
            window.location = response.redirect;
        }
    };
    const handleNotificationClick = (notification) => {
        const notificationsReadLink = notification.dataset.notificationRead;
        const request = new Request(notificationsReadLink, {
            headers: {
                'X-CSRF-Token': token,
            },
            mode: 'cors',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(handleRequestResponseJson)
            .then(onNotificationMarkedAsRead.bind(null, notification))
            .catch(handleResponseError);
    };
    const handleTableClick = (event) => {
        if (event.target.classList.contains('description__read-more')) {
            event.target
                .closest(SELECTOR_MODAL_ITEM)
                .querySelector(SELECTOR_DESC_TEXT)
                .classList.remove(CLASS_ELLIPSIS);

            return;
        }

        const notification = event.target.closest(SELECTOR_MODAL_ITEM);

        if (!notification) {
            return;
        }

        handleNotificationClick(notification);
    };
    const updateNotificationsStatus = () => {
        const notificationsTable = modal.querySelector(SELECTOR_TABLE);
        const notificationsStatusLink = notificationsTable.dataset.notificationsCount;
        const request = new Request(notificationsStatusLink, {
            headers: {
                'X-CSRF-Token': token,
            },
            mode: 'cors',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(handleRequestResponseJson)
            .then(setPendingNotificationCount)
            .catch(handleResponseError);
    };
    const updateUserAvatar = (notificationsInfo) => {
        const pendingNotificationsExist = notificationsInfo.pending;
        const userAvatar = doc.querySelector('.ez-user-menu__avatar-wrapper');

        userAvatar.dataset.count = notificationsInfo.pending;
        userAvatar.classList.toggle('n-pending-notifications', pendingNotificationsExist);

        doc.querySelector('.ez-user-menu__item--notifications').dataset.count = `(${notificationsInfo.pending})`;
    };
    const setPendingNotificationCount = (notificationsInfo) => {
        updateUserAvatar(notificationsInfo);

        const notificationsTable = modal.querySelector(SELECTOR_TABLE);
        const notificationsTotal = notificationsInfo.total;
        const notificationsTotalOld = parseInt(notificationsTable.dataset.notificationsTotal, 10);

        if (notificationsTotal !== notificationsTotalOld) {
            notificationsTable.dataset.notificationsTotal = notificationsTotal;

            refreshNotificationsPage();
        }
    };
    const setNotificationPage = (pageHtml) => {
        const modalResults = modal.querySelector(SELECTOR_MODAL_RESULTS);

        modalResults.innerHTML = pageHtml;
        toggleLoading(true);
    };
    const toggleSpinner = (force) => {
        const spinner = modal.querySelector(SELECTOR_MODAL_SPINNER);

        spinner.classList.toggle(CLASS_MODAL_SPINNER_INVISIBLE, force);
    };
    const toggleResults = (force) => {
        const results = modal.querySelector(SELECTOR_MODAL_RESULTS);

        results.classList.toggle(CLASS_MODAL_RESULTS_INVISIBLE, force);
    };
    const toggleLoading = (force) => {
        toggleSpinner(force);
        toggleResults(!force);
    };
    const fetchNotificationPage = (link) => {
        if (!link) {
            return;
        }

        const request = new Request(link, {
            method: 'GET',
            headers: {
                Accept: 'text/html',
            },
            credentials: 'same-origin',
            mode: 'cors',
        });

        currentPageLink = link;
        toggleLoading(false);
        fetch(request)
            .then(handleRequestResponseText)
            .then(setNotificationPage)
            .catch(handleResponseError);
    };
    const refreshNotificationsPage = () => {
        fetchNotificationPage(currentPageLink);
    };
    const handleModalResultsClick = (event) => {
        handleNotificationsPageChange(event);
        handleTableClick(event);
    };
    const handleNotificationsPageChange = (event) => {
        const { target } = event;
        const isPaginationBtn = target.classList.contains(CLASS_PAGINATION_LINK);

        if (!isPaginationBtn) {
            return;
        }

        const notificationsPageLink = target.href;

        event.preventDefault();
        fetchNotificationPage(notificationsPageLink);
    };

    if (!modal) {
        return;
    }

    const notificationsTable = modal.querySelector(SELECTOR_TABLE);
    currentPageLink = notificationsTable.dataset.notifications;

    modal.querySelectorAll(SELECTOR_MODAL_RESULTS).forEach((link) => link.addEventListener('click', handleModalResultsClick, false));

    updateNotificationsStatus();
    global.setInterval(updateNotificationsStatus, INTERVAL);
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
