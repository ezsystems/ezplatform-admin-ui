(function (global, doc) {
    let notificationsCount = 0;
    const SELECTOR_MODAL_ITEM = '.n-notifications-modal__item';
    const SELECTOR_DESC_TEXT = '.description__text';
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

        markAsRead(event.target.closest('.n-notifications-modal__item'));
    };
    const getNotificationsStatus = () => {
        const requestOptions = {
            headers: {
                'X-CSRF-Token': token
            },
            mode: 'cors',
            credentials: 'same-origin'
        };

        fetch(modal.querySelector('.n-table--notifications').dataset.notificationsCount, requestOptions)
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

        fetch(modal.querySelector('.n-table--notifications').dataset.notifications, requestOptions)
            .then(response => response.text())
            .then(notifications => doc.querySelector('.n-table--notifications tbody').innerHTML = notifications);
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

    [...modal.querySelectorAll('.n-table--notifications .n-table__body')].forEach(btn => btn.addEventListener('click', handleClick, false));

    getNotificationsStatus();
    window.setInterval(getNotificationsStatus, INTERVAL);
})(window, document);
