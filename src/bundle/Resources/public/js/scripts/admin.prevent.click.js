(function(global, doc) {
    global.onbeforeunload = () => {
        doc.querySelector('body').classList.add('ez-prevent-click');

        return null;
    };
})(window, window.document);
