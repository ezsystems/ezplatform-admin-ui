(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezurl';
    const SELECTOR_FIELD_LINK = '.ez-data-source__field--link';
    const SELECTOR_LINK_INPUT = `${SELECTOR_FIELD_LINK} .ez-data-source__input`;
    const SELECTOR_LABEL = '.ez-data-source__label';

    class EzUrlValidator extends global.eZ.BaseFieldValidator {
        validateUrl(event) {
            const input = event.currentTarget;
            const isRequired = input.required;
            const isEmpty = !input.value.trim();
            const isValid = global.eZ.errors.urlRegexp.test(input.value);
            const isError = (isEmpty && isRequired) || (!isEmpty && !isValid);
            const label = input.closest(SELECTOR_FIELD_LINK).querySelector(SELECTOR_LABEL).innerHTML;
            const result = { isError };

            if (isRequired && isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isEmpty && !isValid) {
                result.errorMessage = global.eZ.errors.invalidUrl;
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
                invalidStateSelectors: [
                    SELECTOR_LINK_INPUT,
                    `${SELECTOR_FIELD_LINK} ${SELECTOR_LABEL}`

                ],
                errorNodeSelectors: [`${SELECTOR_FIELD_LINK} .ez-data-source__label-wrapper`],
            },
        ],
    });

    validator.init();

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];
})(window);
