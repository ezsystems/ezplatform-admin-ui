(function(global, doc, eZ, Translator) {
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
    const submitBtns = form.querySelectorAll('[type="submit"]:not([formnovalidate])');
    const getValidationResults = (validator) => {
        const isValid = validator.isValid();
        const validatorName = validator.constructor.name;
        const result = { isValid, validatorName };

        return result;
    };
    const getInvalidTabs = (validator) => {
        return validator.fieldsToValidate.reduce((invalidTabs, field) => {
            const tabPane = field.item.closest('.tab-pane');

            if (tabPane && field.item.classList.contains('is-invalid')) {
                invalidTabs.add(tabPane.id);
            }

            return invalidTabs;
        }, new Set());
    };
    const fields = doc.querySelectorAll('.ez-field-edit');
    const focusOnFirstError = () => {
        const invalidFields = doc.querySelectorAll('.ez-field-edit.is-invalid');

        fields.forEach((field) => field.removeAttribute('tabindex'));
        invalidFields.forEach((field) => field.setAttribute('tabindex', '-1'));

        invalidTab = invalidFields[0].closest('.tab-pane');

        if (invalidTab) {
            const invalidTabLink = doc.querySelector(`a[href="#${invalidTab.id}"]`);

            invalidTabLink.click();
        }

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
        const invalidTabs = validators.map(getInvalidTabs);

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

            doc.querySelectorAll('.ez-tabs__nav-item').forEach((navItem) => {
                navItem.classList.remove('ez-tabs__nav-item--invalid');
            });

            invalidTabs.forEach((invalidInputs) => {
                invalidInputs.forEach((invalidInputKey) => {
                    doc.querySelector(`#item-${invalidInputKey}`).classList.add('ez-tabs__nav-item--invalid');
                });
            });

            focusOnFirstError();
        }
    };

    const isAutosaveEnabled = () => {
        return eZ.adminUiConfig.autosave.enabled && form.querySelector('[name="ezplatform_content_forms_content_edit[autosave]"]');
    };

    if (isAutosaveEnabled()) {
        const autosaveWrapper = doc.querySelector('.ez-content-edit-page-title__autosave-wrapper');
        const AUTOSAVE_SUBMIT_BUTTON_NAME = 'ezplatform_content_forms_content_edit[autosave]';
        let lastSuccessfulAutosave = null;

        setInterval(() => {
            const formData = new FormData(form);

            formData.set(AUTOSAVE_SUBMIT_BUTTON_NAME, true);

            fetch(form.target || window.location.href, { method: 'POST', body: formData })
                .then(eZ.helpers.request.getStatusFromResponse)
                .then(() => {
                    lastSuccessfulAutosave = eZ.helpers.timezone.formatFullDateTime(new Date());

                    autosaveWrapper.classList.remove('ez-content-edit-page-title__autosave-wrapper--failed');
                    autosaveWrapper.classList.add('ez-content-edit-page-title__autosave-wrapper--saved');
                })
                .catch(() => {
                    autosaveWrapper.classList.remove('ez-content-edit-page-title__autosave-wrapper--saved');
                    autosaveWrapper.classList.add('ez-content-edit-page-title__autosave-wrapper--failed');
                })
                .finally(() => {
                    autosaveWrapper.classList.remove('ez-content-edit-page-title__autosave-wrapper--not-saved');

                    if (lastSuccessfulAutosave) {
                        const lastSavedText = Translator.trans(
                            /*@Desc("Last saved draft was on %date%")*/ 'content_edit.last_saved',
                            { date: lastSuccessfulAutosave },
                            'content'
                        );

                        autosaveWrapper.querySelector('.ez-content-edit-page-title__autosave-last-saved').innerHTML = lastSavedText;
                    }
                });
        }, eZ.adminUiConfig.autosave.interval);
    }

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
})(window, window.document, window.eZ, window.Translator);
