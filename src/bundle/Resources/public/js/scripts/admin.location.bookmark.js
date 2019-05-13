(function(global, doc, eZ) {
    const ENDPOINT_BOOKMARK = '/api/ezp/v2/bookmark';
    const SELECTOR_BOOKMARK_WRAPPER = '.ez-add-to-bookmarks';
    const CLASS_BOOKMARK_CHECKED = 'ez-add-to-bookmarks--checked';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const bookmarkWrapper = doc.querySelector(SELECTOR_BOOKMARK_WRAPPER);
    const currentLocationId = parseInt(bookmarkWrapper.getAttribute('data-location-id'), 10);
    const handleUpdateError = eZ.helpers.notification.showErrorNotification;
    let isUpdatingBookmark = false;
    const getResponseStatus = (response) => {
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response.status;
    };
    const onBookmarkUpdated = (isBookmarked) => {
        toggleBookmarkIconState(isBookmarked);
        isUpdatingBookmark = false;
    };
    const updateBookmark = (addBookmark) => {
        if (isUpdatingBookmark) {
            return;
        }

        isUpdatingBookmark = true;

        const method = addBookmark ? 'POST' : 'DELETE';
        const request = new Request(`${ENDPOINT_BOOKMARK}/${currentLocationId}`, {
            method,
            headers: {
                'X-Siteaccess': siteaccess,
                'X-CSRF-Token': token,
            },
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(getResponseStatus)
            .then(onBookmarkUpdated.bind(null, addBookmark))
            .catch(handleUpdateError);
    };
    const isCurrentLocation = (locationId) => {
        return parseInt(locationId, 10) === currentLocationId;
    };
    const toggleBookmarkIconState = (isBookmarked) => {
        bookmarkWrapper.classList.toggle(CLASS_BOOKMARK_CHECKED, isBookmarked);
    };
    const updateBookmarkIconState = ({ detail }) => {
        const { bookmarked, locationId } = detail;

        if (isCurrentLocation(locationId)) {
            toggleBookmarkIconState(bookmarked);
        }
    };
    const checkIsBookmarked = () => {
        return bookmarkWrapper.classList.contains(CLASS_BOOKMARK_CHECKED);
    };
    const onBookmarkChange = () => {
        const addBookmark = !checkIsBookmarked();

        updateBookmark(addBookmark);
    };

    doc.body.addEventListener('ez-bookmark-change', updateBookmarkIconState, false);

    if (bookmarkWrapper) {
        bookmarkWrapper.addEventListener('click', onBookmarkChange, false);
    }
})(window, window.document, window.eZ);
