(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezuser';
    const SELECTOR_INNER_FIELD = '.ibexa-data-source__field';
    const SELECTOR_LABEL = '.ibexa-data-source__label';
    const SELECTOR_FIELD_USERNAME = '.ibexa-data-source__field--username';
    const SELECTOR_FIELD_FIRST = '.ibexa-data-source__field--first';
    const SELECTOR_FIELD_SECOND = '.ibexa-data-source__field--second';
    const SELECTOR_FIELD_EMAIL = '.ibexa-data-source__field--email';
    const SELECTOR_INPUT = '.ibexa-data-source__input';
    const SELECTOR_ERROR_WRAPPER = '.ibexa-form-error';

    class EzUserValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input field value
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzUserValidator
         */
        validateInput({ target }) {
            const fieldContainer = target.closest(SELECTOR_INNER_FIELD);
            const label = fieldContainer.querySelector(SELECTOR_LABEL).innerHTML;
            const isError = target.required && !target.value.trim().length;
            const errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage,
            };
        }

        /**
         * Validates the email input field value
         *
         * @method validateEmailInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzUserValidator
         */
        validateEmailInput({ target }) {
            const isRequired = target.required;
            const isEmpty = !target.value.trim();
            const isValid = eZ.errors.emailRegexp.test(target.value);
            const isError = (isRequired && isEmpty) || !isValid;
            const fieldContainer = target.closest(SELECTOR_INNER_FIELD);
            const result = { isError };

            if (isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', fieldContainer.querySelector(SELECTOR_LABEL).innerHTML);
            } else if (!isValid) {
                result.errorMessage = eZ.errors.invalidEmail;
            }

            return result;
        }

        /**
         * Compares the values of both password inputs
         *
         * @method comparePasswords
         * @param {Event} event
         * @returns {Object}
         * @memberof EzUserValidator
         */
        comparePasswords({ target }) {
            const validationResults = this.validateInput({ target });

            if (validationResults.isError) {
                return validationResults;
            }

            const firstPassword = target
                .closest(this.fieldSelector)
                .querySelector(`${SELECTOR_FIELD_FIRST} ${SELECTOR_INPUT}`)
                .value.trim();
            const secondPassword = target.value.trim();
            const passwordsMatch = firstPassword === secondPassword;
            const requiredNotMatch = target.required && !passwordsMatch;
            const notRequiredNotMatch = !target.required && (firstPassword.length || secondPassword.length) && !passwordsMatch;
            let isError = false;
            let errorMessage;

            if (requiredNotMatch || notRequiredNotMatch) {
                isError = true;
                errorMessage = eZ.errors.notSamePasswords;
            }

            return {
                isError,
                errorMessage,
            };
        }
    }

    const validator = new EzUserValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_USERNAME} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_USERNAME} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_USERNAME} ${SELECTOR_ERROR_WRAPPER}`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_FIRST} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_FIRST} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_FIRST} ${SELECTOR_ERROR_WRAPPER}`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_SECOND} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'comparePasswords',
                invalidStateSelectors: [`${SELECTOR_FIELD_SECOND} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_SECOND} ${SELECTOR_ERROR_WRAPPER}`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_EMAIL} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateEmailInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_EMAIL} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_EMAIL} ${SELECTOR_ERROR_WRAPPER}`],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
