(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ez-field-edit--ezstring';
    const SELECTOR_ERROR_NODE = '.ez-data-source';
    const SELECTOR_SOURCE_INPUT = '.ez-data-source__input';

    class EzStringValidator extends eZ.BaseFieldValidator {
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
            const result = { isError };

            if (isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (isTooShort) {
                result.errorMessage = eZ.errors.tooShort.replace('{fieldName}', label).replace('{minLength}', event.target.dataset.min);
            } else if (isTooLong) {
                result.errorMessage = eZ.errors.tooLong.replace('{fieldName}', label).replace('{maxLength}', event.target.dataset.max);
            }

            return result;
        }
    }

    const validator = new EzStringValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-field-edit--ezstring input',
                eventName: 'blur',
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
                invalidStateSelectors: [SELECTOR_SOURCE_INPUT],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
