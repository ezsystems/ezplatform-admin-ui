(function(global, doc) {
    const editButton = doc.querySelector('.ez-btn--edit-user');
    const languageRadioOption = doc.querySelector('.ez-extra-actions--edit-user.ez-extra-actions--prevent-show [type="radio"]');
    const editActions = doc.querySelector('.ez-extra-actions--edit-user');

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
