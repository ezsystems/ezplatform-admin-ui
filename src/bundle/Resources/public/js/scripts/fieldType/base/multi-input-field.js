(function(global, doc, eZ) {
    class MultiInputFieldValidator extends eZ.BaseFieldValidator {
        constructor({ containerSelectors, ...restProps }) {
            super(restProps);

            this.containerSelectors = containerSelectors;
        }

        toggleInvalidState(isError, config, input) {
            super.toggleInvalidState(isError, config, input);

            this.containerSelectors.forEach((selector) => {
                const invalidSelector = `.${this.classInvalid}`;
                const container = input.closest(selector);
                const method = !!container.querySelector(invalidSelector) ? 'add' : 'remove';

                container.classList[method](this.classInvalid);
            });
        }
    }

    eZ.addConfig('MultiInputFieldValidator', MultiInputFieldValidator);
})(window, window.document, window.eZ);
