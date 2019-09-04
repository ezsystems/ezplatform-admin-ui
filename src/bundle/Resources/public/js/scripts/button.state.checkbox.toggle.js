(function(global, doc) {
    const toggleForms = doc.querySelectorAll('.ez-toggle-btn-state-checkbox');
    const ALL_CHECKED = 'all-checked';
    const ANY_CHECKED = 'any-checked';

    const toggleButtonState = (button, validateCheckboxStatus) => {
        let methodName = 'setAttribute';

        if (validateCheckboxStatus()) {
            methodName = 'removeAttribute';
        }

        button[methodName]('disabled', true);
    };

    toggleForms.forEach((toggleForm) => {
        const checkboxInputs = [...toggleForm.querySelectorAll('input[type="checkbox"]')];
        const button = doc.querySelector(toggleForm.dataset.toggleButtonId);
        const toggleMode = toggleForm.dataset.toggleMode || ANY_CHECKED;
        const validateCheckboxStatus = () =>
            (checkboxInputs.some((el) => el.checked) && ALL_CHECKED === toggleMode) ||
            (checkboxInputs.every((el) => el.checked) && ANY_CHECKED === toggleMode);

        checkboxInputs.forEach((input) =>
            input.addEventListener('change', toggleButtonState.bind(input, button, validateCheckboxStatus), false)
        );
    });
})(window, window.document);
