(function(global, doc) {
    const editButton = doc.querySelector('.ibexa-btn--edit-user');
    const languageRadioOption = doc.querySelector('.ibexa-extra-actions--edit-user.ibexa-extra-actions--prevent-show .ibexa-input--radio');
    const editActions = doc.querySelector('.ibexa-extra-actions--edit-user');

    if (!editActions || !languageRadioOption) {
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
