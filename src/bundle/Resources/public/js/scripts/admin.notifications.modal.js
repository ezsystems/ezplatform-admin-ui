(function (global, doc) {
    let notificationsCount = 0;
    const SELECTOR_MODAL_ITEM = '.n-notifications-modal__item';
    const SELECTOR_DESC_TEXT = '.description__text';
    const SELECTOR_TABLE = '.n-table--notifications';
    const SELECTOR_TABLE_BODY = '.n-table--notifications .n-table__body';
    const CLASS_ELLIPSIS = 'description__text--ellipsis';
    const INTERVAL = 30000;
    const token = document.querySelector('meta[name="CSRF-Token"]').content;
    const modal = doc.querySelector('.n-notifications-modal');
    const markAsRead = (notification) => {
        const requestOptions = {
            headers: {
                'X-CSRF-Token': token
            },
            mode: 'cors',
            credentials: 'same-origin'
        };

        fetch(notification.dataset.notificationRead, requestOptions)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    notification.classList.add('n-notifications-modal__item--read');
                }
                if (data.redirect) {
                    window.location = data.redirect;
                }
            });
    };
    const handleClick = (event) => {
        if (event.target.classList.contains('description__read-more')) {
            event.target.closest(SELECTOR_MODAL_ITEM).querySelector(SELECTOR_DESC_TEXT).classList.remove(CLASS_ELLIPSIS);

            return;
        }

        markAsRead(event.target.closest(SELECTOR_MODAL_ITEM));
    };
    const getNotificationsStatus = () => {
        const requestOptions = {
            headers: {
                'X-CSRF-Token': token,
                'X-Requested-With' : 'XMLHttpRequest'
            },
            mode: 'cors',
            credentials: 'same-origin'
        };

        fetch(modal.querySelector(SELECTOR_TABLE).dataset.notificationsCount, requestOptions)
            .then(response => response.json())
            .then(data => setPendingNotificationCount(data));
    };
    const getNotificationsList = () => {
        const requestOptions = {
            headers: {
                'X-CSRF-Token': token
            },
            mode: 'cors',
            credentials: 'same-origin'
        };

        fetch(modal.querySelector(SELECTOR_TABLE).dataset.notifications, requestOptions)
            .then(response => response.text())
            .then(notifications => doc.querySelector(SELECTOR_TABLE_BODY).innerHTML = notifications);
    };
    const setPendingNotificationCount = (notificationsInfo) => {
        const methodName = notificationsInfo.pending ? 'add' : 'remove';
        const userAvatar = doc.querySelector('.ez-user-menu__avatar-wrapper');

        userAvatar.setAttribute('data-count', notificationsInfo.pending);
        userAvatar.classList[methodName]('n-pending-notifications');
        doc.querySelector('.ez-user-menu__item--notifications').setAttribute('data-count', `(${notificationsInfo.pending})`);

        if (notificationsCount !== notificationsInfo.total) {
            notificationsCount = notificationsInfo.total;

            getNotificationsList();
        }
    };

    if (!modal) {
        return;
    }

    [...modal.querySelectorAll(SELECTOR_TABLE_BODY)].forEach(btn => btn.addEventListener('click', handleClick, false));

    getNotificationsStatus();
    window.setInterval(getNotificationsStatus, INTERVAL);
})(window, document);
