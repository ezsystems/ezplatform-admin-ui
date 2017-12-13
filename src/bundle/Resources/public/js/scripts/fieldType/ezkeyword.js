(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezkeyword';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const SELECTOR_TAGGIFY = '.ez-data-source__taggify';

    class EzKeywordValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the keywords input
         *
         * @method validateKeywords
         * @param {Event} event
         * @returns {Object}
         * @memberof EzKeywordValidator
         */
        validateKeywords(event) {
            const fieldContainer = event.currentTarget.closest(SELECTOR_FIELD);
            const input = fieldContainer.querySelector('.ez-data-source__input-wrapper .ez-data-source__input');
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
            const isRequired = input.required;
            const isEmpty = !(input.value.trim().length);
            const isError = (isEmpty && isRequired);
            const result = { isError };

            if (isError) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return result;
        }
    }

    /**
     * Updates input value with provided value
     *
     * @function updateValue
     * @param {HTMLElement} input
     * @param {Event} event
     */
    const updateValue = (input, event) => {
        input.value = event.detail.tags.map(tag => tag.label).join();

        input.dispatchEvent(new Event('change'));
    };

    [...document.querySelectorAll(SELECTOR_FIELD)].forEach(field => {
        const taggifyContainer = field.querySelector(SELECTOR_TAGGIFY);
        const validator = new EzKeywordValidator({
            classInvalid: 'is-invalid',
            fieldSelector: SELECTOR_FIELD,
            eventsMap: [
                {
                    isValueValidator: false,
                    selector: `${SELECTOR_FIELD} .taggify__input`,
                    eventName: 'blur',
                    callback: 'validateKeywords',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                    invalidStateSelectors: [SELECTOR_TAGGIFY],
                },
                {
                    selector: `${SELECTOR_FIELD} .ez-data-source__input.form-control`,
                    eventName: 'change',
                    callback: 'validateKeywords',
                    errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                    invalidStateSelectors: [SELECTOR_TAGGIFY],
                }
            ],
        });
        const taggify = new window.Taggify({
            containerNode: taggifyContainer,
            displayLabel: false,
            displayInputValues: false,
            // The "," key code
            hotKeys: [188]
        });
        const keywordInput = field.querySelector('.ez-data-source__input-wrapper .ez-data-source__input.form-control');
        const updateKeywords = updateValue.bind(this, keywordInput);

        if (keywordInput.required) {
            taggifyContainer.querySelector('.taggify__input').setAttribute('required', true);
        }

        validator.init();

        if (keywordInput.value.length) {
            taggify.updateTags(keywordInput.value.split(',').map(item => ({
                id: Math.floor((1 + Math.random()) * 0x10000).toString(16),
                label: item
            })));
        }

        taggifyContainer.addEventListener('tagsCreated', updateKeywords, false);
        taggifyContainer.addEventListener('tagRemoved', updateKeywords, false);

        global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            [...global.eZ.fieldTypeValidators, validator] :
            [validator];
    });
})(window);
