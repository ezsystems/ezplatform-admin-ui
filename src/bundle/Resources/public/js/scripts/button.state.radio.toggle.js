(function (global, doc) {
    const toggleForms = [...doc.querySelectorAll('.ez-toggle-btn-state-radio')];

    toggleForms.forEach(toggleForm => {
        const radios = [...toggleForm.querySelectorAll('input[type="radio"]')];
        const toggleButtonState = () => {
            const button = doc.querySelector(toggleForm.dataset.togglebuttonid);
            const oneIsSelected = radios.some(el => el.checked);

            if (oneIsSelected) {
                button['removeAttribute']('disabled', true);
            }
        };

        radios.forEach(radioInput => radioInput.addEventListener('change', toggleButtonState, false));
    });
})(window, document);
