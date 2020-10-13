(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ez-field-edit--ezboolean';
    const SELECTOR_ERROR_NODE = '.ez-data-source';

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
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
            const errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage,
            };
        }

        /**
         * Updates the state of checkbox indicator.
         *
         * @method updateState
         * @param {Event} event
         * @memberof EzBooleanValidator
         */
        updateState(event) {
            const methodName = event.target.checked ? 'add' : 'remove';

            event.target.closest('.ez-data-source__label').classList[methodName]('is-checked');
        }
    }

    const validator = new EzBooleanValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-field-edit--ezboolean input',
                eventName: 'change',
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
            },
            {
                isValueValidator: false,
                selector: '.ez-field-edit--ezboolean input',
                eventName: 'change',
                callback: 'updateState',
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
