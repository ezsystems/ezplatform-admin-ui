(function(global, doc) {
    const editButton = doc.querySelector('.ez-btn--edit');
    const languageRadioOption = doc.querySelector('.ez-extra-actions--edit.ez-extra-actions--prevent-show [type="radio"]');

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
