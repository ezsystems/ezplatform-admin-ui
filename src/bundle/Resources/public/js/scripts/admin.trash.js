(function (global, doc) {
    const form = doc.querySelector('form[name="location_trash"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const firstOptions = form.querySelector('.trash-option-first input');

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

    const enableAllOptions = (event) => {
        const optionsContainers = [...form.querySelectorAll('.trash-option-disabled')];

        optionsContainers.forEach((optionContainer) => {
            optionContainer.classList.remove('trash-option-disabled');
            let inputs = [...optionContainer.getElementsByTagName('input')];

            inputs.forEach((input) => {
                input.disabled = false;
            })
        });
    };

    const allOptionsContainers = form.querySelectorAll('.option-modal-body');

    const toggleSubmitButton = (event) => {
        const isOptionChecked = [...allOptionsContainers].map((container) => {
            let inputs = [...container.getElementsByTagName('input')];
            if (inputs.length === 0) {
                return true;
            }
            const anyChecked = (input) => {
                return input.checked
            };
            return inputs.some(anyChecked)
        });

        let areAllOptionsChecked = isOptionChecked.every((optionIsSelected) => {
            return optionIsSelected
        });

        areAllOptionsChecked ? enableButton(submitButton) : disableButton(submitButton);
    };

    firstOptions.addEventListener('change', enableAllOptions, false);
    form.addEventListener('change', toggleSubmitButton, false);

})(window, document);
