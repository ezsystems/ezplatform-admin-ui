(function(global, doc) {
    const form = doc.querySelector('form[name="location_trash"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const allOptions = form.querySelectorAll('.ez-modal__trash-option');
    const confirmCheckbox = form.querySelector('input[name="location_trash[confirm][]"]');
    const enableButton = (button) => {
        button.disabled = false;
        button.classList.remove('disabled');
    };
    const disableButton = (button) => {
        button.disabled = true;
        button.classList.add('disabled');
    };

    if (!confirmCheckbox) {
        enableButton(submitButton);

        return;
    }

    const toggleSubmitButton = () => {
        const areAllOptionsChecked = [...allOptions].every((option) => {
            const inputs = [...option.querySelectorAll('input')];
            const isInputChecked = (input) => input.checked;

            return inputs.length === 0 || inputs.some(isInputChecked);
        });

        areAllOptionsChecked && confirmCheckbox.checked ? enableButton(submitButton) : disableButton(submitButton);
    };

    form.addEventListener('change', toggleSubmitButton, false);
})(window, document);
