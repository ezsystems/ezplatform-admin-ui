(function(global, doc, $) {
    $(`.ez-tabs a[href="#${global.location.hash.split('#')[1]}"]`).tab('show');

    // Change hash for page-reload
    $('.ez-tabs a').on('shown.bs.tab', (event) => {
        global.location.hash = `${event.target.hash}#tab`;
    });
})(window, window.document, window.jQuery);
