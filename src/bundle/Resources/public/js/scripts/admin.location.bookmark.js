(function (global, doc) {
    const SELECTOR_FORM = 'form[name="location_update_bookmark"]';
    const updateBookmark = () => doc.querySelector(SELECTOR_FORM).submit();

    doc.querySelector('#location_update_bookmark_bookmarked').addEventListener('change', updateBookmark, false);
})(window, document);
