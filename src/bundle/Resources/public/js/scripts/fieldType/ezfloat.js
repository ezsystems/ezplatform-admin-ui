(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezfloat';

    class EzFloatValidator extends global.eZ.BaseFieldValidator {
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
            const isEmpty =  !event.target.value && event.target.value !== '0';
            const isFloat = Number.isInteger(value) || value % 1 !== 0;
            const isLess = value < parseFloat(event.target.getAttribute('min'), 10);
            const isGreater = value > parseFloat(event.target.getAttribute('max'), 10);
            const isError = (isEmpty && isRequired) || !isFloat || isLess || isGreater;
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
            const result = {isError};

            if (isEmpty) {
                result.errorMessage = window.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isFloat) {
                result.errorMessage = window.eZ.errors.isNotFloat.replace('{fieldName}', label);
            } else if (isLess) {
                result.errorMessage = window.eZ.errors.isLess
                    .replace('{fieldName}', label)
                    .replace('{minValue}', event.target.getAttribute('min'));
            } else if (isGreater) {
                result.errorMessage = window.eZ.errors.isGreater
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
                selector: '.ez-field-edit--ezfloat input',
                eventName: 'blur',
                callback: 'validateFloat',
                invalidStateSelectors: [SELECTOR_FIELD],
                errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
            },
        ],
    });

    validator.init();
})(window);
