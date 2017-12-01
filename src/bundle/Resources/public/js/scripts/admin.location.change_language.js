(function (global, doc) {
    const changeLocationLanguage = (event) => {
        window.location = event.currentTarget.value;
    };
    const locationLanguageSwitcher = doc.querySelector('.ez-location-language-change');

    if (locationLanguageSwitcher) {
        locationLanguageSwitcher.addEventListener('change', changeLocationLanguage, false);
    }
})(window, document);
