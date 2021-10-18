(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezfloat';
    const SELECTOR_ERROR_NODE = `${SELECTOR_FIELD} .ibexa-form-error`;

    class EzFloatValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateFloat
         * @param {Event} event
         * @returns {Object}
         * @memberof EzFloatValidator
         */
        validateFloat(event) {
            const isRequired = event.target.required;
            const value = +event.target.value;
            const isEmpty = !event.target.value && event.target.value !== '0';
            const isFloat = Number.isInteger(value) || value % 1 !== 0;
            const isLess = value < parseFloat(event.target.getAttribute('min'));
            const isGreater = value > parseFloat(event.target.getAttribute('max'));
            const isError = (isEmpty && isRequired) || !isFloat || isLess || isGreater;
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ibexa-field-edit__label').innerHTML;
            const result = { isError };

            if (isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isFloat) {
                result.errorMessage = eZ.errors.isNotFloat.replace('{fieldName}', label);
            } else if (isLess) {
                result.errorMessage = eZ.errors.isLess
                    .replace('{fieldName}', label)
                    .replace('{minValue}', event.target.getAttribute('min'));
            } else if (isGreater) {
                result.errorMessage = eZ.errors.isGreater
                    .replace('{fieldName}', label)
                    .replace('{maxValue}', event.target.getAttribute('max'));
            }

            return result;
        }
    }

    const validator = new EzFloatValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ibexa-field-edit--ezfloat input',
                eventName: 'blur',
                callback: 'validateFloat',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
