(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezurl';
    const SELECTOR_FIELD_LINK = '.ibexa-data-source__field--link';
    const SELECTOR_LINK_INPUT = `${SELECTOR_FIELD_LINK} .ibexa-data-source__input`;
    const SELECTOR_LABEL = '.ibexa-data-source__label';
    const SELECTOR_ERROR_NODE = '.ibexa-data-source__field--link .ibexa-form-error';

    class EzUrlValidator extends eZ.BaseFieldValidator {
        validateUrl(event) {
            const input = event.currentTarget;
            const isRequired = input.required;
            const isEmpty = !input.value.trim();
            const isError = isEmpty && isRequired;
            const label = input.closest(SELECTOR_FIELD_LINK).querySelector(SELECTOR_LABEL).innerHTML;
            const result = { isError };

            if (isRequired && isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }
    }

    const validator = new EzUrlValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_LINK_INPUT}`,
                eventName: 'blur',
                callback: 'validateUrl',
                invalidStateSelectors: [SELECTOR_LINK_INPUT, `${SELECTOR_FIELD_LINK} ${SELECTOR_LABEL}`],
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
