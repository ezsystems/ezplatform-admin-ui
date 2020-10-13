(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ez-field-edit--ezuser';
    const SELECTOR_INNER_FIELD = '.ez-data-source__field';
    const SELECTOR_LABEL = '.ez-data-source__label';
    const SELECTOR_LABEL_WRAPPER = '.ez-data-source__label-wrapper';
    const SELECTOR_FIELD_USERNAME = '.ez-data-source__field--username';
    const SELECTOR_FIELD_FIRST = '.ez-data-source__field--first';
    const SELECTOR_FIELD_SECOND = '.ez-data-source__field--second';
    const SELECTOR_FIELD_EMAIL = '.ez-data-source__field--email';
    const SELECTOR_INPUT = '.ez-data-source__input';

    class EzUserValidator extends eZ.BaseFieldValidator {
        /**
         * Updates the state of checkbox indicator.
         *
         * @method updateState
         * @param {Event} event
         * @memberof EzUserValidator
         */
        updateState(event) {
            const methodName = event.currentTarget.checked ? 'add' : 'remove';
            const label = event.currentTarget.closest(SELECTOR_LABEL);

            label.classList[methodName]('is-checked');
        }

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
                errorNodeSelectors: [SELECTOR_FIELD_USERNAME],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_FIRST} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_FIRST} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [SELECTOR_FIELD_FIRST],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_SECOND} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'comparePasswords',
                invalidStateSelectors: [`${SELECTOR_FIELD_SECOND} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [SELECTOR_FIELD_SECOND],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_EMAIL} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateEmailInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_EMAIL} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [SELECTOR_FIELD_EMAIL],
            },
            {
                isValueValidator: false,
                selector: `.ez-data-source__input[type="checkbox"]`,
                eventName: 'change',
                callback: 'updateState',
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
