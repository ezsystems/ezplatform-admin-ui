(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ez-field-edit--ezcountry';
    const SELECTOR_SELECTED = '.ez-custom-dropdown__selection-info';
    const SELECTOR_SOURCE_INPUT = '.ez-data-source__input';
    const EVENT_VALUE_CHANGED = 'valueChanged';
    const SELECTOR_ERROR_NODE = '.ez-data-source';

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
            const hasSelectedOptions = !!fieldContainer.querySelectorAll('.ez-custom-dropdown__selected-item').length;
            const isRequired = fieldContainer.classList.contains('ez-field-edit--required');
            const isError = isRequired && !hasSelectedOptions;
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
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
                selector: '.ez-data-source__input--ezcountry',
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
                invalidStateSelectors: [SELECTOR_SELECTED],
            },
        ],
    });

    validator.init();
    eZ.addConfig('fieldTypeValidators', [validator], true);
    doc.querySelectorAll(SELECTOR_FIELD).forEach((container) => {
        const dropdown = new eZ.core.CustomDropdown({
            container,
            itemsContainer: container.querySelector('.ez-custom-dropdown__items'),
            sourceInput: container.querySelector(SELECTOR_SOURCE_INPUT),
        });

        dropdown.init();
    });
})(window, window.document, window.eZ);
