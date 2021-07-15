(function(global, doc) {
    const toggleFields = doc.querySelectorAll('.ibexa-toggle');
    const toggleInputs = doc.querySelectorAll('.ibexa-toggle input');
    const toggleState = (event) => {
        event.preventDefault();

        const toggler = event.currentTarget;

        if (toggler.classList.contains('ibexa-toggle--is-disabled')) {
            return;
        }

        const isChecked = toggler.classList.toggle('ibexa-toggle--is-checked');

        if (toggler.classList.contains('ibexa-toggle--radio')) {
            const valueToSet = isChecked ? 1 : 0;

            toggler.querySelector(`.form-check input[value="${valueToSet}"]`).checked = true;
        } else {
            toggler.querySelector('.ibexa-toggle__input').checked = isChecked;
        }
    };
    const addFocus = (event) => {
        event.preventDefault();

        const toggler = event.currentTarget.closest('.ibexa-toggle');

        if (toggler.classList.contains('ibexa-toggle--is-disabled')) {
            return;
        }

        toggler.classList.add('ibexa-toggle--is-focused');
    };
    const removeFocus = (event) => {
        event.preventDefault();

        const toggler = event.currentTarget.closest('.ibexa-toggle');

        if (toggler.classList.contains('ibexa-toggle--is-disabled')) {
            return;
        }

        toggler.classList.remove('ibexa-toggle--is-focused');
    };

    toggleFields.forEach((toggleField) => toggleField.addEventListener('click', toggleState, false));
    toggleInputs.forEach((toggleInput) => toggleInput.addEventListener('focus', addFocus, false));
    toggleInputs.forEach((toggleInput) => toggleInput.addEventListener('blur', removeFocus, false));
})(window, window.document);
