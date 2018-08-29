(function (global, doc) {
    const toggleForms = [...doc.querySelectorAll('.ez-toggle-btn-state')];

    toggleForms.forEach(toggleForm => {
        const checkboxes = [...toggleForm.querySelectorAll('.ez-table__cell.ez-table__cell--has-checkbox input[type="checkbox"]')];
        const toggleButtonState = () => {
            const methodName = checkboxes.some(el => el.checked) ? 'removeAttribute' : 'setAttribute';
            const buttonRemove = doc.querySelector(toggleForm.dataset.toggleButtonId);

            buttonRemove[methodName]('disabled', true);
        };

        checkboxes.forEach(checkbox => checkbox.addEventListener('change', toggleButtonState, false));
    });
})(window, document);
