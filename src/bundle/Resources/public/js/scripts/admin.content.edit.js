(function (global, doc, eZ) {
    const enterKeyCode = 13;
    const inputTypeToPreventSubmit = [
        'checkbox',
        'color',
        'date',
        'datetime-local',
        'email',
        'file',
        'image',
        'month',
        'number',
        'radio',
        'range',
        'reset',
        'search',
        'select-one',
        'select-multiple',
        'tel',
        'text',
        'time',
        'url',
    ];
    const form = doc.querySelector('.ez-form-validate');
    const itemAuthor = doc.querySelector('.ez-details-items__author-breadcrumbs');
    const itemAuthorToggler = doc.querySelector('.ez-details-items__toggler');
    const submitBtns = form.querySelectorAll('[type="submit"]:not([formnovalidate])');
    const getValidationResults = (validator) => {
        const isValid = validator.isValid();
        const validatorName = validator.constructor.name;
        const result = { isValid, validatorName };

        return result;
    };
    const fields = doc.querySelectorAll('.ez-field-edit');
    const focusOnFirstError = () => {
        const invalidFields = doc.querySelectorAll('.ez-field-edit.is-invalid');

        fields.forEach((field) => field.removeAttribute('tabindex'));
        invalidFields.forEach((field) => field.setAttribute('tabindex', '-1'));
        invalidFields[0].focus();

        doc.querySelector('.ez-content-item__errors-wrapper').removeAttribute('hidden');
    };
    const clickHandler = (event) => {
        const btn = event.currentTarget;

        if (parseInt(btn.dataset.isFormValid, 10)) {
            return;
        }

        event.preventDefault();

        const validators = eZ.fieldTypeValidators;
        const validationResults = validators.map(getValidationResults);
        const isFormValid = validationResults.every((result) => result.isValid);

        if (isFormValid) {
            btn.dataset.isFormValid = 1;
            // for some reason trying to fire click event inside the event handler flow is impossible
            // the following line breaks the flow so it's possible to fire click event on a button again.
            window.setTimeout(() => btn.click(), 0);
        } else {
            btn.dataset.validatorsWithErrors = Array.from(
                validationResults
                    .filter((result) => !result.isValid)
                    .reduce((total, result) => {
                        total.add(result.validatorName);

                        return total;
                    }, new Set())
            ).join();

            fields.forEach((field) => field.removeAttribute('id'));

            focusOnFirstError();
        }
    };

    form.setAttribute('novalidate', true);
    form.onkeypress = (event) => {
        const keyCode = event.charCode || event.keyCode || 0;
        const activeElementType = typeof doc.activeElement.type !== 'undefined' ? doc.activeElement.type.toLowerCase() : '';

        if (keyCode === enterKeyCode && inputTypeToPreventSubmit.includes(activeElementType)) {
            event.preventDefault();
        }
    };

    submitBtns.forEach((btn) => {
        btn.dataset.isFormValid = 0;
        btn.addEventListener('click', clickHandler, false);
    });

    if (itemAuthorToggler) {
        itemAuthorToggler.addEventListener('click', (event) => {
            event.preventDefault();
            eZ.helpers.tooltips.hideAll();

            itemAuthorToggler.classList.toggle('ez-details-items__toggler--gray');
            itemAuthor.classList.toggle('ez-details-items--collapsed');
        });
    }
})(window, window.document, window.eZ);
