(function(global, doc) {
    global.onbeforeunload = function() {
        doc.querySelector('body').classList.add('ez-prevent-click');

        return null;
    };
})(window, document);
