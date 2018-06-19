(function(global, doc, eZ) {
    const form = doc.querySelector('.ez-form-validate');
    const submitBtns = form.querySelectorAll('[type="submit"]:not([formnovalidate])');

    form.setAttribute('novalidate', true);

    submitBtns.forEach((btn) => {
        const clickHandler = (event) => {
            if (!parseInt(btn.dataset.isFormValid, 10)) {
                event.preventDefault();

                const validators = eZ.fieldTypeValidators;
                const validationResults = validators.map((validator) => {
                    const isValid = validator.isValid();
                    const validatorName = validator.constructor.name;
                    const result = { isValid, validatorName };

                    return result;
                });
                const isFormValid = validationResults.every((result) => result.isValid);

                if (isFormValid) {
                    btn.dataset.isFormValid = 1;
                    // for some reason trying to fire click event inside the event handler flow is impossible
                    // the following line breaks the flow so it's possible to fire click event on a button again.
                    window.setTimeout(() => btn.click(), 0);
                } else {
                    btn.dataset.validatorsWithErrors = Array.from(
                        validationResults.filter((result) => !result.isValid).reduce((total, result) => {
                            total.add(result.validatorName);

                            return total;
                        }, new Set())
                    ).join();
                }
            }
        };
        btn.dataset.isFormValid = 0;
        btn.addEventListener('click', clickHandler, false);
    });
})(window, window.document, window.eZ);
