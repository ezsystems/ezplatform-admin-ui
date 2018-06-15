(function(global, doc) {
    const SELECTOR_FORM = 'form[name="location_update_bookmark"]';
    const SELECTOR_BOOKMARK_CHECKBOX = '#location_update_bookmark_bookmarked';
    const SELECTOR_BOOKMARK_LOCATION_INPUT = '#location_update_bookmark_location';
    const SELECTOR_BOOKMARK_WRAPPER = '.ez-add-to-bookmarks';

    const updateBookmarkLocationInput = doc.querySelector(SELECTOR_BOOKMARK_LOCATION_INPUT);
    const currentLocationId = parseInt(updateBookmarkLocationInput.value, 10);

    const submitBookmarkForm = () => doc.querySelector(SELECTOR_FORM).submit();
    const updateBookmarkCheckbox = (bookmarked) => {
        const checkbox = doc.querySelector(SELECTOR_BOOKMARK_CHECKBOX);

        checkbox.checked = bookmarked;
    };
    const isCurrentLocation = (locationId) => {
        return parseInt(locationId, 10) === currentLocationId;
    };
    const setBookmarkWrapperCheckedClass = (bookmarked) => {
        const wrapperClassList = document.querySelector(SELECTOR_BOOKMARK_WRAPPER).classList;
        const bookmarkCheckedClass = 'ez-add-to-bookmarks--checked';

        if (bookmarked) {
            wrapperClassList.add(bookmarkCheckedClass);
        } else {
            wrapperClassList.remove(bookmarkCheckedClass);
        }
    };
    const updateBookmarkForm = (event) => {
        const { bookmarked, locationId } = event.detail;

        if (isCurrentLocation(locationId)) {
            updateBookmarkCheckbox(bookmarked);
            setBookmarkWrapperCheckedClass(bookmarked);
        }
    };
    const updateBookmarkWrapperClass = (event) => {
        const checked = event.target.checked;

        setBookmarkWrapperCheckedClass(checked);
    };

    doc.body.addEventListener('ez-bookmark-change', updateBookmarkForm, false);
    doc.querySelector(SELECTOR_BOOKMARK_CHECKBOX).addEventListener('change', submitBookmarkForm, false);
    doc.querySelector(SELECTOR_BOOKMARK_CHECKBOX).addEventListener('change', updateBookmarkWrapperClass, false);
})(window, document);
