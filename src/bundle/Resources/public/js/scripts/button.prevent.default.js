(function(global, doc) {
    doc.querySelectorAll('.ibexa-btn--prevented').forEach((btn) => btn.addEventListener('click', (event) => event.preventDefault(), false));
})(window, window.document);
