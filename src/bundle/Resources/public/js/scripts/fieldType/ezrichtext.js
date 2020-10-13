(function(global, doc, eZ, React, ReactDOM) {
    const SELECTOR_FIELD = '.ez-field-edit--ezrichtext';
    const SELECTOR_INPUT = '.ez-data-source__richtext';
    const SELECTOR_ERROR_NODE = '.ez-field-edit__label-wrapper';
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
