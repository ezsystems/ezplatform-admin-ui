(function(global, doc) {
    const changeLocationLanguage = (event) => {
        global.location = event.currentTarget.value;
    };
    const locationLanguageSwitchers = doc.querySelectorAll('.ez-location-language-change');

    locationLanguageSwitchers.forEach((locationLanguageSwitcher) => {
        locationLanguageSwitcher.addEventListener('change', changeLocationLanguage, false);
    });
})(window, window.document);
