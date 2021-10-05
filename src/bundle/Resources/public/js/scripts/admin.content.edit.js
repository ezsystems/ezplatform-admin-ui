(function(global, doc, eZ, Translator) {
    const ENTER_KEY_CODE = 13;
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
    const menuButtonsToValidate = doc.querySelectorAll('button[data-validate]');
    const getValidationResults = (validator) => {
        const isValid = validator.isValid();
        const validatorName = validator.constructor.name;
        const result = { isValid, validatorName };

        return result;
    };
    const getInvalidSections = (validator) => {
        return validator.fieldsToValidate.reduce((invalidSections, field) => {
            const section = field.item.closest('.ibexa-anchor-navigation-sections__section');

            if (section && field.item.classList.contains('is-invalid')) {
                invalidSections.add(section.dataset.anchorSectionId);
            }

            return invalidSections;
        }, new Set());
    };
    const fields = doc.querySelectorAll('.ez-field-edit');
    const focusOnFirstError = () => {
        const invalidFields = doc.querySelectorAll('.ez-field-edit.is-invalid');
        const invalidSection = invalidFields[0].closest('.ibexa-anchor-navigation-sections__section');

        fields.forEach((field) => field.removeAttribute('tabindex'));
        invalidFields.forEach((field) => field.setAttribute('tabindex', '-1'));

        if (invalidSection) {
            const { anchorSectionId } = invalidSection.dataset;
            const invalidButton = doc.querySelector(`[data-anchor-target-section="${anchorSectionId}"`);

            invalidButton.click();
        }

        invalidFields[0].focus();
    };
    const clickHandler = (event) => {
        const btn = event.currentTarget;

        if (parseInt(btn.dataset.isFormValid, 10)) {
            return;
        }

        event.preventDefault();

        if (isFormValid(btn)) {
            // for some reason trying to fire click event inside the event handler flow is impossible
            // the following line breaks the flow so it's possible to fire click event on a button again.
            global.setTimeout(() => btn.click(), 0);
        }
    };
    const validateHandler = (event) => {
        event.preventDefault();

        const btn = event.currentTarget;

        btn.dataset.isFormValid = 0;

        isFormValid(btn);
    };
    const isFormValid = (btn) => {
        const validators = eZ.fieldTypeValidators;
        const validationResults = validators.map(getValidationResults);
        const isFormValid = validationResults.every((result) => result.isValid);
        const invalidSections = validators.map(getInvalidSections);

        if (isFormValid) {
            btn.dataset.isFormValid = 1;

            return true;
        }

        const allValidatorsWithErrors = validationResults.filter((result) => !result.isValid).map((result) => result.validatorName);

            btn.dataset.validatorsWithErrors = [...new Set(allValidatorsWithErrors)].join();
            fields.forEach((field) => field.removeAttribute('id'));

        doc.querySelectorAll('.ibexa-anchor-navigation-menu__btn').forEach((anchorBtn) => {
            anchorBtn.classList.remove('ibexa-anchor-navigation-menu__btn--invalid');
        });

        invalidSections.forEach((sections) => {
            sections.forEach((invalidSectionId) => {
                doc.querySelector(`[data-anchor-target-section='${invalidSectionId}']`).classList.add(
                        'ibexa-anchor-navigation-menu__btn--invalid'
                    );
                });
            });

        focusOnFirstError();

        return false;};
    const isAutosaveEnabled = () => {
        return eZ.adminUiConfig.autosave.enabled && form.querySelector('[name="ezplatform_content_forms_content_edit[autosave]"]');
    };
    const fitSections = () => {
        const contentColumn = doc.querySelector('.ibexa-main-container__content-column');
        const lastSection = doc.querySelector('.ibexa-anchor-navigation-sections .ibexa-anchor-navigation-sections__section:last-child');

        if (lastSection && lastSection.offsetHeight) {
            const lastSectionHeight = lastSection.offsetHeight;
            const headerHeight = doc.querySelector('.ibexa-edit-header').offsetHeight;
            const contentColumnBodyHeight = contentColumn.offsetHeight - headerHeight;
            const heightDiff = contentColumnBodyHeight - lastSectionHeight;

            if (heightDiff > 0) {
                lastSection.style.paddingBottom = `${heightDiff}px`;
            }
        }
    };

    if (isAutosaveEnabled()) {
        const autosaveWrapper = doc.querySelector('.ibexa-autosave');
        const AUTOSAVE_SUBMIT_BUTTON_NAME = 'ezplatform_content_forms_content_edit[autosave]';
        let lastSuccessfulAutosave = null;

        setInterval(() => {
            const formData = new FormData(form);

            formData.set(AUTOSAVE_SUBMIT_BUTTON_NAME, true);

            fetch(form.target || window.location.href, { method: 'POST', body: formData })
                .then(eZ.helpers.request.getStatusFromResponse)
                .then(() => {
                    lastSuccessfulAutosave = eZ.helpers.timezone.formatFullDateTime(new Date());

                    autosaveWrapper?.classList.remove('ibexa-autosave--failed');
                    autosaveWrapper?.classList.add('ibexa-autosave--saved');
                })
                .catch(() => {
                    autosaveWrapper?.classList.remove('ibexa-autosave--saved');
                    autosaveWrapper?.classList.add('ibexa-autosave--failed');
                })
                .finally(() => {
                    autosaveWrapper?.classList.remove('ibexa-autosave--not-saved');

                    if (lastSuccessfulAutosave) {
                        const lastSavedText = Translator.trans(
                            /*@Desc("Last saved draft was on %date%")*/ 'content_edit.last_saved',
                            { date: lastSuccessfulAutosave },
                            'content'
                        );

                        if (autosaveWrapper) {
                            autosaveWrapper.querySelector('.ibexa-autosave__last-saved').innerHTML = lastSavedText;
                        }
                    }
                });
        }, eZ.adminUiConfig.autosave.interval);
    }

    form.setAttribute('novalidate', true);
    form.onkeypress = (event) => {
        const keyCode = event.charCode || event.keyCode || 0;
        const activeElementType = typeof doc.activeElement.type !== 'undefined' ? doc.activeElement.type.toLowerCase() : '';

        if (keyCode === ENTER_KEY_CODE && inputTypeToPreventSubmit.includes(activeElementType)) {
            event.preventDefault();
        }
    };

    submitBtns.forEach((btn) => {
        btn.dataset.isFormValid = 0;
        btn.addEventListener('click', clickHandler, false);
    });

    fitSections();

    menuButtonsToValidate.forEach((btn) => {
        btn.addEventListener('click', validateHandler, false);
    });

})(window, window.document, window.eZ, window.Translator);
