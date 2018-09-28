(function (global, doc) {
    const toggleForms = [...doc.querySelectorAll('.ez-toggle-btn-state-radio')];

    toggleForms.forEach((toggleForm) => {
        const radioInputs = [...toggleForm.querySelectorAll('input[type="radio"]')];
        const toggleButtonState = () => {
            const button = doc.querySelector(toggleForm.dataset.toggleButtonId);
            const oneIsSelected = radioInputs.some(el => el.checked);

            if (oneIsSelected) {
                button['removeAttribute']('disabled', true);
            }
        };

        radioInputs.forEach(radioInput => radioInput.addEventListener('change', toggleButtonState, false));
    });
})(window, document);
