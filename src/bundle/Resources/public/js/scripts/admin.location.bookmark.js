(function (global, doc) {
    const updateBookmark = event => {
        doc.querySelector('form[name="location_update_bookmark"]').submit();
    };

    doc.querySelector('#location_update_bookmark_bookmarked').addEventListener('change', updateBookmark, false);
})(window, document);
