(function (global, doc) {
    const updateMainTranslation = event => {
        doc.querySelector('#main_translation_update_language_code').value = event.target.value;
        doc.querySelector('form[name="main_translation_update"]').submit();
    };

    [...doc.querySelectorAll('input[name="updateMainTranslation"]')].forEach(input => input.addEventListener('change', updateMainTranslation, false));
})(window, document);
