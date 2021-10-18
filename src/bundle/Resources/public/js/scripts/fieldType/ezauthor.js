(function(global, doc, eZ) {
    const SELECTOR_REMOVE_AUTHOR = '.ibexa-btn--remove-author';
    const SELECTOR_AUTHOR = '.ibexa-data-source__author';
    const SELECTOR_FIELD = '.ibexa-field-edit--ezauthor';
    const SELECTOR_LABEL = '.ibexa-data-source__label';
    const SELECTOR_FIELD_EMAIL = '.ibexa-data-source__field--email';
    const SELECTOR_FIELD_NAME = '.ibexa-data-source__field--name';

    class EzAuthorValidator extends eZ.MultiInputFieldValidator {
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
            const errorMessage = eZ.errors.emptyField.replace('{fieldName}', fieldNode.querySelector(SELECTOR_LABEL).innerHTML);

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
            const isValid = eZ.errors.emailRegexp.test(input.value);
            const isError = (isRequired && isEmpty) || (!isEmpty && !isValid);
            const label = input.closest(SELECTOR_FIELD_EMAIL).querySelector(SELECTOR_LABEL).innerHTML;
            const result = { isError };

            if (isRequired && isEmpty) {
                result.errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            } else if (!isEmpty && !isValid) {
                result.errorMessage = eZ.errors.invalidEmail;
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
            return template.replace(/__index__/g, parentNode.dataset.nextAuthorId);
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

            parentNode.querySelectorAll(SELECTOR_REMOVE_AUTHOR).forEach((btn) => {
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

        toggleBulkDeleteButtonState(event) {
            const container = event.target.closest(SELECTOR_FIELD);
            const checkboxes = container.querySelectorAll('.ibexa-input--checkbox');
            const isAnyCheckboxSelected = [...checkboxes].some((checkbox) => checkbox.checked);
            const bulkDeleteButton = container.querySelector('.ibexa-btn--bulk-remove-author');

            bulkDeleteButton.toggleAttribute('disabled', !isAnyCheckboxSelected);
        }

        removeSelectedItems(event) {
            const container = event.target.closest(SELECTOR_FIELD);
            const selectedCheckboxes = container.querySelectorAll('.ibexa-input--checkbox:checked');
            const bulkDeleteButton = container.querySelector('.ibexa-btn--bulk-remove-author');

            selectedCheckboxes.forEach((checkbox) => checkbox.closest(SELECTOR_AUTHOR).remove());

            bulkDeleteButton.setAttribute('disabled', 'disabled');

            this.updateDisabledState(container);
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
            const node = event.target.closest('.ibexa-field-edit__data .ibexa-data-source');

            node.insertAdjacentHTML('beforeend', this.setIndex(authorNode, template));
            authorNode.dataset.nextAuthorId++;

            this.reinit();
            this.updateDisabledState(authorNode);
            eZ.helpers.tooltips.parse(node);
            eZ.helpers.tooltips.hideAll();
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
            const nodes = [fieldNode, authorRow];

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

            return selectors.reduce((total, selector) => total.concat([...authorRow.querySelectorAll(selector)]), []);
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

            doc.querySelectorAll(this.fieldSelector).forEach((field) => this.updateDisabledState(field));
        }
    }

    const validator = new EzAuthorValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        containerSelectors: ['.ibexa-data-source__author', '.ibexa-field-edit--ezauthor'],
        eventsMap: [
            {
                selector: `.ibexa-data-source__author ${SELECTOR_FIELD_NAME} .ibexa-data-source__input`,
                eventName: 'blur',
                callback: 'validateName',
                invalidStateSelectors: [
                    SELECTOR_FIELD_NAME,
                    `${SELECTOR_FIELD_NAME} .ibexa-data-source__input`,
                    `${SELECTOR_FIELD_NAME} .ibexa-data-source__label`,
                ],
                errorNodeSelectors: [`${SELECTOR_FIELD_NAME} .ibexa-form-error`],
            },
            {
                selector: `.ibexa-data-source__author ${SELECTOR_FIELD_EMAIL} .ibexa-data-source__input`,
                eventName: 'blur',
                callback: 'validateEmail',
                invalidStateSelectors: [
                    SELECTOR_FIELD_EMAIL,
                    `${SELECTOR_FIELD_EMAIL} .ibexa-data-source__input`,
                    `${SELECTOR_FIELD_EMAIL} .ibexa-data-source__label`,
                ],
                errorNodeSelectors: [`${SELECTOR_FIELD_EMAIL} .ibexa-form-error`],
            },
            {
                isValueValidator: false,
                selector: SELECTOR_REMOVE_AUTHOR,
                eventName: 'click',
                callback: 'removeItem',
            },
            {
                isValueValidator: false,
                selector: '.ibexa-data-source__author .ibexa-input--checkbox',
                eventName: 'change',
                callback: 'toggleBulkDeleteButtonState',
            },
            {
                isValueValidator: false,
                selector: '.ibexa-btn--bulk-remove-author',
                eventName: 'click',
                callback: 'removeSelectedItems',
            },
            {
                isValueValidator: false,
                selector: '.ibexa-btn--add-author',
                eventName: 'click',
                callback: 'addItem',
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);
})(window, window.document, window.eZ);
