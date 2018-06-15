(function(global, doc) {
    const SELECTOR_FORM = 'form[name="location_update_bookmark"]';
    const SELECTOR_BOOKMARK_CHECKBOX = '#location_update_bookmark_bookmarked';
    const SELECTOR_BOOKMARK_LOCATION_INPUT = '#location_update_bookmark_location';
    const SELECTOR_BOOKMARK_WRAPPER = '.ez-add-to-bookmarks';
    const CLASS_BOOKMARK_CHECKED = 'ez-add-to-bookmarks--checked';

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
    const toggleBookmarkIconState = (isBookmarked) => {
        const wrapper = doc.querySelector(SELECTOR_BOOKMARK_WRAPPER);

        wrapper.classList.toggle(CLASS_BOOKMARK_CHECKED, isBookmarked);
    };
    const updateBookmarkForm = (event) => {
        const { bookmarked, locationId } = event.detail;

        if (isCurrentLocation(locationId)) {
            updateBookmarkCheckbox(bookmarked);
            toggleBookmarkIconState(bookmarked);
        }
    };
    const updateBookmarkIconState = (event) => {
        const checked = event.target.checked;

        toggleBookmarkIconState(checked);
    };

    doc.body.addEventListener('ez-bookmark-change', updateBookmarkForm, false);
    doc.querySelector(SELECTOR_BOOKMARK_CHECKBOX).addEventListener('change', submitBookmarkForm, false);
    doc.querySelector(SELECTOR_BOOKMARK_CHECKBOX).addEventListener('change', updateBookmarkIconState, false);
})(window, document);
