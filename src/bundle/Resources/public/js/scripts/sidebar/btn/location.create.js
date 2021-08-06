(function(global, doc) {
    const createActions = doc.querySelector('.ibexa-extra-actions--create');
    const btns = createActions.querySelectorAll('.form-check [type="radio"]');
    const form = createActions.querySelector('form');

    btns.forEach((btn) => btn.addEventListener('change', () => form.submit(), false));
})(window, window.document);
