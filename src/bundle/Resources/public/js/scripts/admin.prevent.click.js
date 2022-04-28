(function(global, doc) {
    global.onbeforeunload = () => {
        doc.querySelector('body').classList.add('ez-prevent-click');

        return null;
    };

    global.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            doc.querySelector('body').classList.remove('ez-prevent-click');
        }
    });
})(window, window.document);
