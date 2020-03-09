(function(global, doc) {
    const languageSelector = doc.querySelector('.ez-search-form__language-selector');
    const submitForm = (event) => {
        event.target.closest('form').submit();
    };

    if (!languageSelector) {
        return;
    }

    languageSelector.addEventListener('change', submitForm, false);
})(window, document);
