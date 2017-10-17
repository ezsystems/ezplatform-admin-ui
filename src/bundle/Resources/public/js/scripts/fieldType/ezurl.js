(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit-ezurl';
    const SELECTOR_FIELD_LINK = '.ez-sub-field-link';

    class EzUrlValidator extends global.eZ.BaseFieldValidator {
        validateUrl(event) {
            const isRequired = event.target.required;
            const isEmpty = !event.target.value.trim();
            const isValid = global.eZ.errors.urlRegexp.test(event.target.value);
            const isError = (isEmpty && isRequired) || !isValid;
            const label = event.target.closest(SELECTOR_FIELD_LINK).querySelector('.form-control-label').innerHTML;
            const result = {isError};

            if (isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isValid) {
                result.errorMessage = global.eZ.errors.invalidUrl;
            }

            return result;
        }
    };

    const validator = new EzUrlValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-field-edit-ezurl .ez-sub-field-link input',
                eventName: 'blur',
                callback: 'validateUrl',
                invalidStateSelectors: [SELECTOR_FIELD],
                errorNodeSelectors: ['.ez-sub-field-link .ez-sub-field-text-zone'],
            },
        ],
    });

    validator.init();
})(window);
