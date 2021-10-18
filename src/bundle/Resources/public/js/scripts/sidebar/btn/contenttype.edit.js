(function(global, doc) {
    const editButton = doc.querySelector('.ibexa-btn--edit');
    const languageRadioOption = doc.querySelector('.ibexa-extra-actions--edit.ibexa-extra-actions--prevent-show .ibexa-input--radio');
    const editActions = doc.querySelector('.ibexa-extra-actions--edit');
    const btns = editActions.querySelectorAll('.form-check [type="radio"]');
    const changeHandler = () => {
        const form = doc.querySelector('.ibexa-extra-actions--edit form');

        form.submit();
    };

    btns.forEach((btn) => btn.addEventListener('change', changeHandler, false));

    if (!languageRadioOption) {
        return;
    }

    editButton.addEventListener(
        'click',
        () => {
            languageRadioOption.checked = true;
            changeHandler();
        },
        false
    );
})(window, window.document);
