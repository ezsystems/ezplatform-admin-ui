(function(global, doc, eZ) {
    const getId = () => doc.querySelector('meta[name="UserId"]').content;

    eZ.addConfig('helpers.user', {
        getId,
    });
})(window, window.document, window.eZ);
