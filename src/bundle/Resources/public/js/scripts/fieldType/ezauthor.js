(function (global, doc) {
    const SELECTOR_REMOVE_AUTHOR = '.ez-js-remove-author';
    const SELECTOR_AUTHOR_ROW = '.ez-author-row';
    const SELECTOR_FIELD = '.ez-field-edit-ezauthor';
    const SELECTOR_FORM_LABEL = '.form-control-label';
    const SELECTOR_FIELD_EMAIL = '.ez-sub-field-email';
    const SELECTOR_FIELD_NAME = '.ez-sub-field-name';

    class EzAuthorValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the 'name' input field value
         *
         * @method validateName
         * @param {Event} event
         * @returns {Object}
         * @memberof EzAuthorValidator
         */
        validateName(event) {
            const isError = !event.target.value.trim() && event.target.required;
            const fieldNode = event.target.closest(SELECTOR_FIELD_NAME);
            const errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', fieldNode.querySelector(SELECTOR_FORM_LABEL).innerHTML);

            return {
                isError: isError,
                errorMessage: errorMessage,
            };
        }

        /**
         * Validates the 'email' input field value
         *
         * @method validateEmail
         * @param {Event} event
         * @returns {Object}
         * @memberof EzAuthorValidator
         */
        validateEmail(event) {
            const isEmpty =  !event.target.value.trim();
            const isValid = global.eZ.errors.emailRegexp.test(event.target.value);
            const isError = (event.target.required && isEmpty) || !isValid;
            const fieldNode = event.target.closest(SELECTOR_FIELD_EMAIL);
            const result = {isError};

            if (isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', fieldNode.querySelector(SELECTOR_FORM_LABEL).innerHTML);
            } else if (!isValid) {
                result.errorMessage = global.eZ.errors.invalidEmail;
            }

            return result;
        }

        /**
         * Sets an index to template.
         *
         * @method setIndex
         * @param {HTMLElement} parentNode
         * @param {String} template
         * @returns {String}
         * @memberof EzAuthorValidator
         */
        setIndex(parentNode, template) {
            return template.replace(/__index__/g, parentNode.querySelectorAll(SELECTOR_AUTHOR_ROW).length)
        }

        /**
         * Updates the disable state.
         *
         * @method updateDisabledState
         * @param {HTMLElement} parentNode
         * @memberof EzAuthorValidator
         */
        updateDisabledState(parentNode) {
            const isEnabled = parentNode.querySelectorAll(SELECTOR_AUTHOR_ROW).length > 1;

            [...parentNode.querySelectorAll(SELECTOR_REMOVE_AUTHOR)].forEach(btn => {
                if (isEnabled) {
                    btn.removeAttribute('disabled');
                } else {
                    btn.setAttribute('disabled', true);
                }
            });
        }

        /**
         * Removes an item.
         *
         * @method removeItem
         * @param {Event} event
         * @memberof EzAuthorValidator
         */
        removeItem(event) {
            const authorNode = event.target.closest(SELECTOR_FIELD);

            event.target.closest(SELECTOR_AUTHOR_ROW).remove();

            this.updateDisabledState(authorNode);
        }

        /**
         * Adds an item.
         *
         * @method addItem
         * @param {Event} event
         * @memberof EzAuthorValidator
         */
        addItem(event) {
            const authorNode = event.target.closest(SELECTOR_FIELD);
            const template = authorNode.dataset.prototype;
            const node = event.target.closest('.ez-field-edit-ui');

            node.insertAdjacentHTML('beforeend', this.setIndex(authorNode, template));

            this.reinit();
            this.updateDisabledState(authorNode);
        }

        /**
         * Finds the nodes to add validation state
         *
         * @method findValidationStateNodes
         * @param {HTMLElement} fieldNode
         * @param {HTMLElement} input
         * @param {Array} selectors
         * @returns {Array}
         * @memberof BaseFieldValidator
         */
        findValidationStateNodes(fieldNode, input, selectors) {
            const authorRow = input.closest(SELECTOR_AUTHOR_ROW);
            const nodes = [
                fieldNode,
                authorRow
            ];

            return selectors.reduce((total, selector) => total.concat([...authorRow.querySelectorAll(selector)]), nodes);
        }

        /**
         * Finds the error containers
         *
         * @method findErrorContainers
         * @param {HTMLElement} fieldNode
         * @param {HTMLElement} input
         * @param {Array} selectors
         * @returns {Array}
         * @memberof BaseFieldValidator
         */
        findErrorContainers(fieldNode, input, selectors) {
            const authorRow = input.closest(SELECTOR_AUTHOR_ROW);
            const nodes = [
                fieldNode,
                authorRow
            ];

            return selectors.reduce((total, selector) => total.concat([...authorRow.querySelectorAll(selector)]), nodes);
        }

        /**
         * Finds the existing error nodes
         *
         * @method findExistingErrorNodes
         * @param {HTMLElement} fieldNode
         * @param {HTMLElement} input
         * @param {Array} selectors
         * @returns {Array}
         * @memberof BaseFieldValidator
         */
        findExistingErrorNodes(fieldNode, input, selectors) {
            return selectors.reduce((total, selector) => total.concat([...input.closest(SELECTOR_AUTHOR_ROW).querySelectorAll(selector)]), []);
        }
    };

    const validator = new EzAuthorValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-author-row .ez-sub-field-name input',
                eventName: 'blur',
                callback: 'validateName',
                invalidStateSelectors: [SELECTOR_FIELD, SELECTOR_AUTHOR_ROW, SELECTOR_FIELD_NAME],
                errorNodeSelectors: ['.ez-sub-field-name .ez-sub-field-text-zone'],
            },
            {
                selector: '.ez-author-row .ez-sub-field-email input',
                eventName: 'blur',
                callback: 'validateEmail',
                invalidStateSelectors: [SELECTOR_FIELD, SELECTOR_AUTHOR_ROW, SELECTOR_FIELD_EMAIL],
                errorNodeSelectors: ['.ez-sub-field-email .ez-sub-field-text-zone'],
            },
            {
                selector: SELECTOR_REMOVE_AUTHOR,
                eventName: 'click',
                callback: 'removeItem',
            },
            {
                selector: '.ez-js-add-author',
                eventName: 'click',
                callback: 'addItem',
            },
        ],
    });

    validator.init();
})(window, document);
