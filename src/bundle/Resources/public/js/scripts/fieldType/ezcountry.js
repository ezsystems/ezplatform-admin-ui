(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ez-field-edit--ezcountry';

    class EzCountryValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the select box value
         *
         * @method validateSelection
         * @param {Event} event
         * @returns {Object}
         * @memberof EzCountryValidator
         */
        validateSelection(event) {
            const isRequired = event.target.required;
            const isEmpty = !event.target.value.trim().length;
            const isError = isEmpty && isRequired;
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
            const result = { isError };

            if (isError) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }
    }

    const validator = new EzCountryValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} .ez-data-source__input`,
                eventName: 'blur',
                callback: 'validateSelection',
                errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
