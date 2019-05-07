(function(global, doc) {
    doc.querySelectorAll('.ez-btn--prevented').forEach((btn) => btn.addEventListener('click', (event) => event.preventDefault(), false));
})(window, window.document);
