(function (global, doc, $) {
    const location = global.location;

    /**
     * Returns tab identifier from hash or url parameter
     *
     * @function createContentTypeDataMap
     * @param {Location} location
     * @returns {string}
     */
    const getTabId = (location) => {
        return location.hash.split('#')[1]
            || new URL(location.href).searchParams.get("_fragment")
            || '';
    };

    $('.ez-tabs a[href="#' + getTabId(location) + '"]').tab('show');

    // Change hash for page-reload
    $('.ez-tabs a').on('shown.bs.tab', function (e) {
        global.location.hash = e.target.hash + '#tab';
    })
})(window, document, window.jQuery);
