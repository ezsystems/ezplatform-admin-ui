(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezcountry';
    const SELECTOR_SELECTED = '.ibexa-dropdown__selection-info';
    const EVENT_VALUE_CHANGED = 'change';
    const SELECTOR_ERROR_NODE = '.ibexa-form-error';

    class EzCountryValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the country field value
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzCountryValidator
         */
        validateInput(event) {
            const fieldContainer = event.currentTarget.closest(SELECTOR_FIELD);
            const hasSelectedOptions = !!fieldContainer.querySelector('.ibexa-data-source__input').value;
            const isRequired = fieldContainer.classList.contains('ibexa-field-edit--required');
            const isError = isRequired && !hasSelectedOptions;
            const label = fieldContainer.querySelector('.ibexa-field-edit__label').innerHTML;
            const errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage,
            };
        }
    }
    const validator = new EzCountryValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ibexa-data-source__input--ezcountry',
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
                invalidStateSelectors: [SELECTOR_SELECTED],
            },
        ],
    });

    validator.init();
    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
