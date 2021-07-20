(function(global, doc) {
    const toggleFields = doc.querySelectorAll('.ibexa-toggle');
    const toggleInputs = doc.querySelectorAll('.ibexa-toggle input');
    const toggleState = (event) => {
        event.preventDefault();

        if (event.currentTarget.classList.contains('ibexa-toggle--is-disabled')) {
            return;
        }

        event.currentTarget.classList.toggle('ibexa-toggle--is-checked');

        const isChecked = event.currentTarget.classList.contains('ibexa-toggle--is-checked');
        const valueToSet = isChecked ? 1 : 0;

        if (event.currentTarget.classList.contains('ibexa-toggle--radio')) {
            event.currentTarget.querySelector(`.form-check input[value="${valueToSet}"]`).checked = true;
        } else {
            event.currentTarget.querySelector('.ibexa-toggle__input').checked = isChecked;
        }
    };
    const addFocus = (event) => {
        event.preventDefault();

        if (event.currentTarget.classList.contains('ibexa-toggle--is-disabled')) {
            return;
        }

        event.currentTarget.closest('.ibexa-toggle').classList.add('ibexa-toggle--is-focused');
    };
    const removeFocus = (event) => {
        event.preventDefault();

        if (event.currentTarget.classList.contains('ibexa-toggle--is-disabled')) {
            return;
        }

        event.currentTarget.closest('.ibexa-toggle').classList.remove('ibexa-toggle--is-focused');
    };

    toggleFields.forEach((toggleField) => toggleField.addEventListener('click', toggleState, false));
    toggleInputs.forEach((toggleInput) => toggleInput.addEventListener('focus', addFocus, false));
    toggleInputs.forEach((toggleInput) => toggleInput.addEventListener('blur', removeFocus, false));
})(window, window.document);
