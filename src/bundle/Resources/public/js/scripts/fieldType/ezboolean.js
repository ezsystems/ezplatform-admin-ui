(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezboolean';
    const SELECTOR_ERROR_NODE = '.ibexa-form-error';

    class EzBooleanValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input field value
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzBooleanValidator
         */
        validateInput(event) {
            const isError = !event.target.checked && event.target.required;
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ibexa-field-edit__label').innerHTML;
            const errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage,
            };
        }
    }

    const validator = new EzBooleanValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ibexa-field-edit--ezboolean input',
                eventName: 'change',
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
