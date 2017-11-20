(function () {
    var url = document.location.toString();
    if (url.match('#')) {
        $('.ez-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.ez-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash + '#tab';
    })
})();
