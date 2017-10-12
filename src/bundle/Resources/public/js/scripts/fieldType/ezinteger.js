(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit-ezinteger';

    class EzIntegerValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInteger
         * @param {Event} event
         * @returns {Object}
         * @memberof EzIntegerValidator
         */
        validateInteger(event) {
            const isRequired = event.target.required;
            const value = +event.target.value;
            const isEmpty =  !event.target.value && event.target.value !== '0';
            const isInteger = Number.isInteger(value);
            const isLess = value < parseInt(event.target.getAttribute('min'), 10);
            const isGreater = value > parseInt(event.target.getAttribute('max'), 10);
            const isError = (isEmpty && isRequired) || !isInteger || isLess || isGreater;
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.form-control-label').innerHTML;
            const result = {isError};

            if (isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isInteger) {
                result.errorMessage = global.eZ.errors.isNotInteger.replace('{fieldName}', label);
            } else if (isLess) {
                result.errorMessage = global.eZ.errors.isLess
                    .replace('{fieldName}', label)
                    .replace('{minValue}', event.target.getAttribute('min'));
            } else if (isGreater) {
                result.errorMessage = global.eZ.errors.isGreater
                    .replace('{fieldName}', label)
                    .replace('{maxValue}', event.target.getAttribute('max'));
            }

            return result;
        }
    }

    const validator = new EzIntegerValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-field-edit-ezinteger input',
                eventName: 'blur',
                callback: 'validateInteger',
                invalidStateSelectors: [SELECTOR_FIELD],
                errorNodeSelectors: ['.ez-field-edit-text-zone'],
            },
        ],
    });

    validator.init();
})(window);
