(function(global, doc, eZ) {
    const SELECTOR_FIELD_LABEL = '.ibexa-field-edit__label-wrapper .ibexa-field-edit__label';

    class BaseFileFieldValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         */
        validateInput(event) {
            const input = event.currentTarget;
            const dataContainer = this.fieldContainer.querySelector('.ibexa-field-edit__data');
            const label = this.fieldContainer.querySelector(SELECTOR_FIELD_LABEL).innerHTML;
            const isRequired = input.required || this.fieldContainer.classList.contains('ibexa-field-edit--required');
            const dataMaxSize = +input.dataset.maxFileSize;
            const maxFileSize = parseInt(dataMaxSize, 10);
            const isEmpty = input.files && !input.files.length && dataContainer && !dataContainer.hasAttribute('hidden');
            let result = { isError: false };

            if (isRequired && isEmpty) {
                result = {
                    isError: true,
                    errorMessage: eZ.errors.emptyField.replace('{fieldName}', label),
                };
            }

            if (!isEmpty && maxFileSize > 0 && input.files[0] && input.files[0].size > maxFileSize) {
                result = this.validateFileSize(event);
            }

            return result;
        }

        validateFileSize(event) {
            return this.showFileSizeError();
        }

        /**
         * Displays an error message: file size exceeds maximum value
         *
         * @method showFileSizeNotice
         * @returns {Object}
         */
        showFileSizeError() {
            const label = this.fieldContainer.querySelector(SELECTOR_FIELD_LABEL).innerHTML;
            const result = {
                isError: true,
                errorMessage: eZ.errors.invalidFileSize.replace('{fieldName}', label),
            };

            return result;
        }
    }

    eZ.addConfig('BaseFileFieldValidator', BaseFileFieldValidator);
})(window, window.document, window.eZ);
