(function (global, doc) {
    const SELECTOR_REMOVE_AUTHOR = '.ez-btn--remove-author';
    const SELECTOR_AUTHOR = '.ez-data-source__author';
    const SELECTOR_FIELD = '.ez-field-edit--ezauthor';
    const SELECTOR_LABEL = '.ez-data-source__label';
    const SELECTOR_FIELD_EMAIL = '.ez-data-source__field--email';
    const SELECTOR_FIELD_NAME = '.ez-data-source__field--name';

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
            const errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', fieldNode.querySelector(SELECTOR_LABEL).innerHTML);

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
            const input = event.currentTarget;
            const isRequired = input.required;
            const isEmpty = !input.value.trim();
            const isValid = global.eZ.errors.emailRegexp.test(input.value);
            const isError = (isRequired && isEmpty) || (!isEmpty && !isValid);
            const label = input.closest(SELECTOR_FIELD).querySelector('.ez-field-edit__label').innerHTML;
            const result = { isError };

            if (isRequired && isEmpty) {
                result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isEmpty && !isValid) {
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
            return template.replace(/__index__/g, parentNode.querySelectorAll(SELECTOR_AUTHOR).length)
        }

        /**
         * Updates the disable state.
         *
         * @method updateDisabledState
         * @param {HTMLElement} parentNode
         * @memberof EzAuthorValidator
         */
        updateDisabledState(parentNode) {
            const isEnabled = parentNode.querySelectorAll(SELECTOR_AUTHOR).length > 1;

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

            event.target.closest(SELECTOR_AUTHOR).remove();

            this.updateDisabledState(authorNode);
            this.reinit();
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
            const template = authorNode.dataset.template;
            const node = event.target.closest('.ez-field-edit__data .ez-data-source');

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
         * @memberof EzAuthorValidator
         */
        findValidationStateNodes(fieldNode, input, selectors) {
            const authorRow = input.closest(SELECTOR_AUTHOR);
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
         * @memberof EzAuthorValidator
         */
        findErrorContainers(fieldNode, input, selectors) {
            const authorRow = input.closest(SELECTOR_AUTHOR);
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
         * @memberof EzAuthorValidator
         */
        findExistingErrorNodes(fieldNode, input, selectors) {
            return selectors.reduce((total, selector) => total.concat([...input.closest(SELECTOR_AUTHOR).querySelectorAll(selector)]), []);
        }

        /**
         * Attaches event listeners based on a config.
         *
         * @method init
         * @memberof EzAuthorValidator
         */
        init() {
            super.init();

            [...doc.querySelectorAll(this.fieldSelector)].forEach(field => this.updateDisabledState(field));
        }
    }

    const validator = new EzAuthorValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `.ez-data-source__author ${SELECTOR_FIELD_NAME} .ez-data-source__input`,
                eventName: 'blur',
                callback: 'validateName',
                invalidStateSelectors: [
                    SELECTOR_FIELD_NAME,
                    `${SELECTOR_FIELD_NAME} .ez-data-source__input`,
                    `${SELECTOR_FIELD_NAME} .ez-data-source__label`

                ],
                errorNodeSelectors: [`${SELECTOR_FIELD_NAME} .ez-data-source__label-wrapper`],
            },
            {
                selector: `.ez-data-source__author ${SELECTOR_FIELD_EMAIL} .ez-data-source__input`,
                eventName: 'blur',
                callback: 'validateEmail',
                invalidStateSelectors: [
                    SELECTOR_FIELD_EMAIL,
                    `${SELECTOR_FIELD_EMAIL} .ez-data-source__input`,
                    `${SELECTOR_FIELD_EMAIL} .ez-data-source__label`
                ],
                errorNodeSelectors: [`${SELECTOR_FIELD_EMAIL} .ez-data-source__label-wrapper`],
            },
            {
                isValueValidator: false,
                selector: SELECTOR_REMOVE_AUTHOR,
                eventName: 'click',
                callback: 'removeItem',
            },
            {
                isValueValidator: false,
                selector: '.ez-btn--add-author',
                eventName: 'click',
                callback: 'addItem',
            },
        ],
    });

    validator.init();

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];
})(window, document);
