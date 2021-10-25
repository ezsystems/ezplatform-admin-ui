(function(global, doc) {
    const languageSelector = doc.querySelector('.ibexa-filters__item--language-selector .ibexa-filters__select');
    const submitForm = (event) => {
        event.target.closest('form').submit();
    };

    if (!languageSelector) {
        return;
    }

    languageSelector.addEventListener('change', submitForm, false);
})(window, document);
