(function (global, doc) {
    const eZ = global.eZ = global.eZ || {};

    eZ.BaseFieldValidator = class BaseFieldValidator {
        constructor(config) {
            this.classInvalid = config.classInvalid;
            this.eventsMap = config.eventsMap;
            this.fieldSelector = config.fieldSelector;
        }

        attachEvent(eventName, selector, callback) {
            [...doc.querySelectorAll(selector)].forEach(item => item.addEventListener(eventName, callback, false));
        }

        removeEvent(eventName, selector, callback) {
            [...doc.querySelectorAll(selector)].forEach(item => item.removeEventListener(eventName, callback, false));
        }

        findValidationStateNodes(fieldNode, input, selectors) {
            return selectors.reduce((total, selector) => total.concat([...fieldNode.querySelectorAll(selector)]), []);
        }

        toggleInvalidState(isError, config, input) {
            const fieldNode = input.closest(this.fieldSelector);
            const methodName = isError ? 'add' : 'remove';
            const nodes = this.findValidationStateNodes(fieldNode, input, config.invalidStateSelectors);

            fieldNode.classList[methodName](this.classInvalid);
            input.classList[methodName](this.classInvalid);

            nodes.forEach(el => el.classList[methodName](this.classInvalid));
        }

        createErrorNode(message) {
            const errorNode = doc.createElement('em');

            errorNode.classList.add('ez-field-error');
            errorNode.innerHTML = message;

            return errorNode;
        }

        findErrorContainers(fieldNode, input, selectors) {
            return selectors.reduce((total, selector) => total.concat([...fieldNode.querySelectorAll(selector)]), []);
        }

        findExistingErrorNodes(fieldNode, input, selectors) {
            return this.findErrorContainers(fieldNode, input, selectors);
        }

        toggleErrorMessage(validationResult, config, input) {
            const fieldNode = input.closest(this.fieldSelector);
            const nodes = this.findErrorContainers(fieldNode, input, config.errorNodeSelectors);
            const existingErrorSelectors = config.errorNodeSelectors.map(selector => selector + ' .ez-field-error');
            const existingErrorNodes = this.findExistingErrorNodes(fieldNode, input, existingErrorSelectors);

            existingErrorNodes.forEach(el => el.remove());

            if (validationResult.isError) {
                const errorNode = this.createErrorNode(validationResult.errorMessage);

                nodes.forEach(el => el.append(errorNode));
            }
        }

        validateField(config, event) {
            const validationResult = this[config.callback](event);

            if (!validationResult) {
                return;
            }

            this.toggleInvalidState(validationResult.isError, config, event.target);
            this.toggleErrorMessage(validationResult, config, event.target);
        }

        init() {
            this.eventsMap.forEach(eventConfig => {
                eventConfig.validateField = this.validateField.bind(this, eventConfig);

                this.attachEvent(eventConfig.eventName, eventConfig.selector, eventConfig.validateField);
            });
        }

        reinit() {
            this.eventsMap.forEach(({eventName, selector, validateField}) => this.removeEvent(eventName, selector, validateField));
            this.init();
        }
    };
})(window, document);
