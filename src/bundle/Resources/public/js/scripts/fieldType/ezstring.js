(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezstring';

    class EzStringValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzStringValidator
         */
        validateInput(event) {
            const isRequired = event.target.required;
            const isEmpty = !event.target.value;
            const isTooShort = event.target.value.length < parseInt(event.target.dataset.min, 10);
            const isTooLong = event.target.value.length > parseInt(event.target.dataset.max, 10);
            const isError = (isEmpty && isRequired) || isTooShort || isTooLong;
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
            const result = {isError};

            if (isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (isTooShort) {
                result.errorMessage = global.eZ.errors.tooShort.replace('{fieldName}', label).replace('{minLength}', event.target.dataset.min);
            } else if (isTooLong) {
                result.errorMessage = global.eZ.errors.tooLong.replace('{fieldName}', label).replace('{maxLength}', event.target.dataset.max);
            }

            return result;
        }
    };

    const validator = new EzStringValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-field-edit--ezstring input',
                eventName: 'blur',
                callback: 'validateInput',
                errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                invalidStateSelectors: ['.ez-data-source__input']
            },
        ],
    });

    validator.init();

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];
})(window);
