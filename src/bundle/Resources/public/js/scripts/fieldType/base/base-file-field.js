(function (global) {
    const eZ = global.eZ = global.eZ || {};
    const SELECTOR_FIELD_LABEL = '.ez-field-edit__label-wrapper .col-form-legend';

    class BaseFileFieldValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         */
        validateInput(event) {
            const input = event.currentTarget;
            const label = this.fieldContainer.querySelector(SELECTOR_FIELD_LABEL).innerHTML;
            const result = { isError: false };

            if (input.required && !input.files.length) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }

        /**
         * Displays an error message: file size exceeds maximum value
         *
         * @method showSizeError
         */
        showSizeError() {
            const label = this.fieldContainer.querySelector(SELECTOR_FIELD_LABEL).innerHTML;
            const result = {
                isError: true,
                errorMessage: global.eZ.errors.invalidFileSize.replace('{fieldName}', label)
            };

            return result;
        }
    }

    eZ.BaseFileFieldValidator = BaseFileFieldValidator;
})(window);
