(function(global, doc) {
    const toggleForms = doc.querySelectorAll('.ez-toggle-btn-state');

    toggleForms.forEach((toggleForm) => {
        const checkboxes = [...toggleForm.querySelectorAll('.ibexa-table__cell--has-checkbox .ibexa-input--checkbox')];
        const buttonRemove = doc.querySelector(toggleForm.dataset.toggleButtonId);

        if (!buttonRemove) {
            return;
        }

        const toggleButtonState = () => {
            const isAnythingSelected = checkboxes.some((el) => el.checked);

            buttonRemove.disabled = !isAnythingSelected;
        };

        toggleButtonState();
        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', toggleButtonState, false));
    });
})(window, window.document);
