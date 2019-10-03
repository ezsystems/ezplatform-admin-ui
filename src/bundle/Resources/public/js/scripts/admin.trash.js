(function(global, doc) {
    const form = doc.querySelector('form[name="location_trash"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const firstOptions = form.querySelector('.ez-modal__trash-option--first input');
    const enableButton = (button) => {
        button.disabled = false;
        button.classList.remove('disabled');
    };
    const disableButton = (button) => {
        button.disabled = true;
        button.classList.add('disabled');
    };

    if (!firstOptions) {
        enableButton(submitButton);

        return;
    }

    const allOptions = form.querySelectorAll('.ez-modal__trash-option');
    const enableAllOptions = () => {
        const disabledOptions = [...form.querySelectorAll('.ez-modal__trash-option--disabled')];

        disabledOptions.forEach((disabledOption) => {
            const inputs = [...disabledOption.querySelectorAll('input')];

            disabledOption.classList.remove('ez-modal__trash-option--disabled');

            inputs.forEach((input) => {
                input.disabled = false;
            });
        });
    };
    const toggleSubmitButton = () => {
        const areAllOptionsChecked = [...allOptions].every((option) => {
            const inputs = [...option.querySelectorAll('input')];
            const isInputChecked = (input) => input.checked;

            return inputs.length === 0 || inputs.some(isInputChecked);
        });

        areAllOptionsChecked ? enableButton(submitButton) : disableButton(submitButton);
    };

    firstOptions.addEventListener('change', enableAllOptions, false);
    form.addEventListener('change', toggleSubmitButton, false);
})(window, document);
