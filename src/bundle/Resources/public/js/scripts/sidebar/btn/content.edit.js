(function(global, doc) {
    const editButton = doc.querySelector('.ibexa-btn--edit');
    const languageRadioOption = doc.querySelector('.ibexa-extra-actions--edit.ibexa-extra-actions--prevent-show [type="radio"]');

    if (!languageRadioOption) {
        return;
    }

    editButton.addEventListener(
        'click',
        () => {
            languageRadioOption.checked = true;
            languageRadioOption.dispatchEvent(new CustomEvent('change'));
        },
        false
    );
})(window, window.document);
