(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit-eztext';

    class EzTextValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the textarea field value
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzTextValidator
         */
        validateInput(event) {
            const isError = event.target.required && !event.target.value.trim();
            const label = event.target.closest(SELECTOR_FIELD).querySelector('.form-control-label').innerHTML;
            const errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage
            };
        }
    };

    const validator = new EzTextValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-field-edit-eztext textarea',
                eventName: 'blur',
                callback: 'validateInput',
                invalidStateSelectors: ['.ez-field-edit-eztext'],
                errorNodeSelectors: ['.ez-field-edit-text-zone'],
            },
        ],
    });

    validator.init();
})(window);
