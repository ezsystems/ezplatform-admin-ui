(function(global, doc, eZ) {
    const SELECTOR_FIELD = '.ez-field-edit--ezrichtext';
    const SELECTOR_INPUT = '.ez-data-source__richtext';

    class EzRichTextValidator extends eZ.BaseFieldValidator {
        constructor(config) {
            super(config);

            this.alloyEditor = config.alloyEditor;
        }
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzRichTextValidator
         */
        validateInput(event) {
            const fieldContainer = event.currentTarget.closest(SELECTOR_FIELD);
            const isRequired = fieldContainer.classList.contains('ez-field-edit--required');
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
            const isEmpty = !this.alloyEditor.get('nativeEditor').getData().length;
            const isError = isRequired && isEmpty;
            const result = { isError };

            if (isError) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }
    }

    doc.querySelectorAll(`${SELECTOR_FIELD} ${SELECTOR_INPUT}`).forEach((container) => {
        const richtext = new eZ.BaseRichText();
        const alloyEditor = richtext.init(container);

        const validator = new EzRichTextValidator({
            classInvalid: 'is-invalid',
            fieldContainer: container.closest(SELECTOR_FIELD),
            alloyEditor,
            eventsMap: [
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'input',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                },
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                },
            ],
        });

        validator.init();

        eZ.addConfig('fieldTypeValidators', [validator], true);
    });
})(window, window.document, window.eZ);
