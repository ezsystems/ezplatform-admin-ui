(function(global, doc, eZ, React, ReactDOM) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezrichtext';
    const SELECTOR_INPUT = '.ibexa-data-source__richtext';
    const SELECTOR_ERROR_NODE = '.ibexa-form-error';
    const selectContent = (config) => {
        const udwContainer = document.querySelector('#react-udw');
        const confirmHandler = (items) => {
            if (typeof config.onConfirm === 'function') {
                config.onConfirm(items);
            }

            ReactDOM.unmountComponentAtNode(udwContainer);
        };
        const cancelHandler = () => {
            if (typeof config.onCancel === 'function') {
                config.onCancel();
            }

            ReactDOM.unmountComponentAtNode(udwContainer);
        };
        const mergedConfig = Object.assign({}, config, {
            onConfirm: confirmHandler,
            onCancel: cancelHandler,
        });

        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, mergedConfig), udwContainer);
    };

    eZ.addConfig('richText.alloyEditor.callbacks.selectContent', selectContent);

    class EzRichTextValidator extends eZ.BaseFieldValidator {
        constructor(config) {
            super(config);

            this.richtextEditor = config.richtextEditor;
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
            const isRequired = fieldContainer.classList.contains('ibexa-field-edit--required');
            const label = fieldContainer.querySelector('.ibexa-field-edit__label').innerHTML;
            const isEmpty = !this.richtextEditor.getData().length;
            const isError = isRequired && isEmpty;
            const result = { isError };
            if (isError) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }
            return result;
        }
    }

    doc.querySelectorAll(`${SELECTOR_FIELD} ${SELECTOR_INPUT}`).forEach((container) => {
        const richtextEditor = new eZ.BaseRichText();

        richtextEditor.init(container);

        const validator = new EzRichTextValidator({
            classInvalid: 'is-invalid',
            fieldContainer: container.closest(SELECTOR_FIELD),
            richtextEditor,
            eventsMap: [
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'input',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_ERROR_NODE],
                },
                {
                    selector: SELECTOR_INPUT,
                    eventName: 'blur',
                    callback: 'validateInput',
                    errorNodeSelectors: [SELECTOR_ERROR_NODE],
                },
            ],
        });

        validator.init();

        eZ.addConfig('fieldTypeValidators', [validator], true);
    });
})(window, window.document, window.eZ, window.React, window.ReactDOM);
