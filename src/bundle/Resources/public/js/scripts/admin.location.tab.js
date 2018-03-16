(function (global, doc, $) {
    $('.ez-tabs a[href="#' + global.location.hash.split('#')[1] + '"]').tab('show');

    // Change hash for page-reload
    $('.ez-tabs a').on('shown.bs.tab', function (e) {
        global.location.hash = e.target.hash + '#tab';
    })
})(window, document, window.jQuery);
