(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezuser';
    const SELECTOR_INNER_FIELD = '.ez-data-source__field';
    const SELECTOR_LABEL = '.ez-data-source__label';
    const SELECTOR_LABEL_WRAPPER = '.ez-data-source__label-wrapper';
    const SELECTOR_FIELD_USERNAME = '.ez-data-source__field--username';
    const SELECTOR_FIELD_FIRST = '.ez-data-source__field--first';
    const SELECTOR_FIELD_SECOND = '.ez-data-source__field--second';
    const SELECTOR_FIELD_EMAIL = '.ez-data-source__field--email';
    const SELECTOR_INPUT = '.ez-data-source__input';

    class EzUserValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input field value
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzUserValidator
         */
        validateInput({target}) {
            const fieldContainer = target.closest(SELECTOR_INNER_FIELD);
            const label = fieldContainer.querySelector(SELECTOR_LABEL).innerHTML;
            const isError = target.required && !target.value.trim().length;
            const errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage
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
        validateEmailInput({target}) {
            const isRequired = target.required;
            const isEmpty = !target.value.trim();
            const isValid = global.eZ.errors.emailRegexp.test(target.value);
            const isError = (isRequired && isEmpty) || !isValid;
            const fieldContainer = target.closest(SELECTOR_INNER_FIELD);
            const result = {isError};

            if (isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', fieldContainer.querySelector(SELECTOR_LABEL).innerHTML);
            } else if (!isValid) {
                result.errorMessage = global.eZ.errors.invalidEmail;
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
        comparePasswords({target}) {
            const validationResults = this.validateInput({target});

            if (validationResults.isError) {
                return validationResults;
            }

            const firstPassword = target
                .closest(this.fieldSelector)
                .querySelector(`${SELECTOR_FIELD_FIRST} ${SELECTOR_INPUT}`)
                .value.trim();
            const secondPassword = target.value.trim();
            let isError = false;
            let errorMessage;

            if (target.required && firstPassword !== secondPassword) {
                isError = true;
                errorMessage = global.eZ.errors.notSamePasswords;
            }

            return {
                isError,
                errorMessage
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
                errorNodeSelectors: [`${SELECTOR_FIELD_USERNAME} ${SELECTOR_LABEL_WRAPPER}`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_FIRST} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_FIRST} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_FIRST} ${SELECTOR_LABEL_WRAPPER}`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_SECOND} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'comparePasswords',
                invalidStateSelectors: [`${SELECTOR_FIELD_SECOND} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_SECOND} ${SELECTOR_LABEL_WRAPPER}`],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FIELD_EMAIL} ${SELECTOR_INPUT}`,
                eventName: 'blur',
                callback: 'validateEmailInput',
                invalidStateSelectors: [`${SELECTOR_FIELD_EMAIL} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [`${SELECTOR_FIELD_EMAIL} ${SELECTOR_LABEL_WRAPPER}`],
            },
        ]
    });

    validator.init();

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];
})(window);
