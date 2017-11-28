(function (global, doc) {
    const form = doc.querySelector('.ez-form-validate');
    const submitBtns = form.querySelectorAll('[type="submit"]:not([formnovalidate])');

    form.setAttribute('novalidate', true);

    submitBtns.forEach(btn => {
        const clickHandler = (event) => {
            if (!parseInt(btn.dataset.isFormValid, 10)) {
                event.preventDefault();

                const validators = global.eZ.fieldTypeValidators;
                const isFormValid = validators.map(validator => validator.isValid()).every(result => result);

                if (isFormValid) {
                    btn.dataset.isFormValid = 1;
                    // for some reason trying to fire click event inside the event handler flow is impossible
                    // the following line breaks the flow so it's possible to fire click event on a button again.
                    window.setTimeout(() => btn.click(), 0);
                }
            }
        };
        btn.dataset.isFormValid = 0;
        btn.addEventListener('click', clickHandler, false);
    });
})(window, document);
