(function(global, doc, eZ, React, ReactDOM, Translator) {
    let currentPageLink = null;
    const SELECTOR_MODAL_ITEM = '.ez-notifications-modal__item';
    const SELECTOR_MODAL_RESULTS = '.ez-notifications-modal__results';
    const SELECTOR_DESC_TEXT = '.description__text';
    const SELECTOR_TABLE = '.n-table--notifications';
    const CLASS_ELLIPSIS = 'description__text--ellipsis';
    const CLASS_PAGINATION_LINK = 'page-link';
    const CLASS_MODAL_LOADING = 'ez-notifications-modal--loading';
    const INTERVAL = 30000;
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const modal = doc.querySelector('.ez-notifications-modal');
    const handleResponseError = eZ.helpers.notification.showErrorNotification;
    const getJsonFromResponse = eZ.helpers.request.getJsonFromResponse;
    const getTextFromResponse = eZ.helpers.request.getTextFromResponse;
    const markAsRead = (notification, response) => {
        if (response.status === 'success') {
            notification.classList.add('ez-notifications-modal__item--read');
        }

        if (response.redirect) {
            window.location = response.redirect;
        }
    };
    const handleNotificationClick = (notification) => {
        const notificationReadLink = notification.dataset.notificationRead;
        const request = new Request(notificationReadLink, {
            headers: {
                'X-CSRF-Token': token,
            },
            mode: 'cors',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(getJsonFromResponse)
            .then(markAsRead.bind(null, notification))
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
    const getNotificationsStatus = () => {
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
            .then(getJsonFromResponse)
            .then(setPendingNotificationCount)
            .catch(handleResponseError);
    };
    const updatePendingNotificationsView = (notificationsInfo) => {
        const pendingNotificationsExist = notificationsInfo.pending;
        const userAvatar = doc.querySelector('.ez-user-menu__avatar-wrapper');

        userAvatar.dataset.count = notificationsInfo.pending;
        userAvatar.classList.toggle('n-pending-notifications', pendingNotificationsExist);

        doc.querySelector('.ez-user-menu__item--notifications').dataset.count = `(${notificationsInfo.pending})`;
    };
    const setPendingNotificationCount = (notificationsInfo) => {
        updatePendingNotificationsView(notificationsInfo);

        const notificationsTable = modal.querySelector(SELECTOR_TABLE);
        const notificationsTotal = notificationsInfo.total;
        const notificationsTotalOld = parseInt(notificationsTable.dataset.notificationsTotal, 10);

        if (notificationsTotal !== notificationsTotalOld) {
            notificationsTable.dataset.notificationsTotal = notificationsTotal;

            fetchNotificationPage(currentPageLink);
        }
    };
    const showNotificationPage = (pageHtml) => {
        const modalResults = modal.querySelector(SELECTOR_MODAL_RESULTS);

        modalResults.innerHTML = pageHtml;
        toggleLoading(false);
    };
    const toggleLoading = (show) => {
        modal.classList.toggle(CLASS_MODAL_LOADING, show);
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
        toggleLoading(true);
        fetch(request)
            .then(getTextFromResponse)
            .then(showNotificationPage)
            .catch(handleResponseError);
    };
    const handleModalResultsClick = (event) => {
        const isPaginationBtn = event.target.classList.contains(CLASS_PAGINATION_LINK);

        if (isPaginationBtn) {
            handleNotificationsPageChange(event);
            return;
        }

        handleTableClick(event);
    };
    const handleNotificationsPageChange = (event) => {
        event.preventDefault();

        const notificationsPageLink = event.target.href;

        fetchNotificationPage(notificationsPageLink);
    };

    if (!modal) {
        return;
    }

    const notificationsTable = modal.querySelector(SELECTOR_TABLE);
    currentPageLink = notificationsTable.dataset.notifications;

    modal.querySelectorAll(SELECTOR_MODAL_RESULTS).forEach((link) => link.addEventListener('click', handleModalResultsClick, false));

    getNotificationsStatus();
    global.setInterval(getNotificationsStatus, INTERVAL);
})(window, document, window.eZ, window.React, window.ReactDOM, window.Translator);
