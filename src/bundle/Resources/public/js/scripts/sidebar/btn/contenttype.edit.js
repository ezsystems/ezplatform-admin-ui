(function(global, doc) {
    const editButton = doc.querySelector('.ez-btn--edit');
    const languageRadioOption = doc.querySelector('.ez-extra-actions--edit.ez-extra-actions--prevent-show [type="radio"]');
    const editActions = doc.querySelector('.ez-extra-actions--edit');
    const btns = editActions.querySelectorAll('.form-check [type="radio"]');
    const changeHandler = () => {
        const form = doc.querySelector('.ez-extra-actions--edit form');

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
