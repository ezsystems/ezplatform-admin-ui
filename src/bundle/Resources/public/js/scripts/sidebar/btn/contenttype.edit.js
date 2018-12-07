(function (global, doc) {
    const editButton = doc.querySelector('.ez-btn--edit');
    const languageRadioOption = doc.querySelector('.ez-extra-actions--edit.ez-extra-actions--prevent-show [type="radio"]');

    if (!languageRadioOption) {
        return;
    }

    editButton.addEventListener('click', () => {
        const form = doc.querySelector('.ez-extra-actions--edit form');

        languageRadioOption.checked = true;
        form.submit();
    }, false);
})(window, document);
