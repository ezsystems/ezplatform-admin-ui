(function (global, doc) {
    const CLASS_HIDDEN = 'ez-data-source__options--hidden';
    const CLASS_SELECTED = 'option-selected';
    const SELECTOR_FIELD = '.ez-field-edit--ezselection';
    const SELECTOR_OPTIONS = '.ez-data-source__options';
    const SELECTOR_SELECTED = '.ez-data-source__selected';
    const SELECTOR_SOURCE_INPUT = '.ez-data-source__input';
    const EVENT_VALUE_CHANGED = 'valueChanged';

    class EzSelectionValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the textarea field value
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzSelectionValidator
         */
        validateInput(event) {
            const fieldContainer = event.currentTarget.closest(SELECTOR_FIELD);
            const hasSelectedOptions = !!fieldContainer.querySelectorAll(`${SELECTOR_SELECTED} .selected-item`).length;
            const isRequired = fieldContainer.classList.contains('ez-field-edit--required');
            const isError = isRequired && !hasSelectedOptions;
            const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
            const errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);

            return {
                isError,
                errorMessage
            };
        }
    }

    const validator = new EzSelectionValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: '.ez-data-source__input',
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
                invalidStateSelectors: [SELECTOR_SELECTED],
            },
        ],
    });

    validator.init();

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];

    [...doc.querySelectorAll(SELECTOR_FIELD)].forEach(container => {
        const createSelectedItem = (value, label) => `<li class="selected-item" data-value="${value}">${label}<span class="remove-selection"></span></li>`;
        const handleSelection = (element, selected) => {
            const value = element.dataset.value;
            const CSSMethodName = selected ? 'add' : 'remove';
            const isSingleSelect = !container.querySelector(SELECTOR_SOURCE_INPUT).multiple;

            if (isSingleSelect && selected) {
                hideOptions();
                clearCurrentSelection();
            }

            container.querySelector(`${SELECTOR_SOURCE_INPUT} [value="${value}"]`).selected = selected;
            container.querySelector(`${SELECTOR_OPTIONS} [data-value="${value}"]`).classList[CSSMethodName](CLASS_SELECTED);

            if (selected) {
                container.querySelector(SELECTOR_SELECTED).insertAdjacentHTML('beforeend', createSelectedItem(value, element.innerHTML));
            } else {
                container.querySelector(`${SELECTOR_SELECTED} [data-value="${value}"]`).remove();
            }

            if (isSingleSelect && !selected) {
                hideOptions();
                selectFirstItem();
            }

            container.querySelector(SELECTOR_SOURCE_INPUT).dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
        };
        const selectFirstItem = () => {
            const firstOption = container.querySelector(`${SELECTOR_OPTIONS} li`);

            firstOption.classList.add(CLASS_SELECTED);

            container
                .querySelector(SELECTOR_SELECTED)
                .insertAdjacentHTML('beforeend', createSelectedItem(firstOption.dataset.value, firstOption.innerHTML));
        };
        const clearCurrentSelection = () => {
            [...container.querySelectorAll(`${SELECTOR_SOURCE_INPUT} option`)].forEach(option => option.selected = false);
            [...container.querySelectorAll(`${SELECTOR_OPTIONS} .option-selected`)].forEach(option => option.classList.remove(CLASS_SELECTED));
            container.querySelector(SELECTOR_SELECTED).innerHTML = '';
        };
        const handleClickOutside = (event) => {
            if (event.target.closest(SELECTOR_SELECTED) || event.target.closest(SELECTOR_OPTIONS)) {
                return;
            }

            hideOptions();
            container.querySelector('.ez-data-source__input').dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));

        };
        const hideOptions = () => container.querySelector(SELECTOR_OPTIONS).classList.add(CLASS_HIDDEN);
        const handleClickOnInput = (event) => {
            if (event.target.classList.contains('remove-selection')) {
                handleSelection(event.target.closest('li'), false);

                return;
            }

            const options = container.querySelector(SELECTOR_OPTIONS);
            const methodName = options.classList.contains(CLASS_HIDDEN) ? 'addEventListener' : 'removeEventListener';

            options.classList.toggle(CLASS_HIDDEN);
            doc.querySelector('body')[methodName]('click', handleClickOutside, false);
        };
        const handleClickOnOption = (event) => handleSelection(event.target, !event.target.classList.contains(CLASS_SELECTED));

        selectFirstItem();

        container.querySelector(SELECTOR_SELECTED).addEventListener('click', handleClickOnInput, false);
        [...container.querySelectorAll(`${SELECTOR_OPTIONS} li`)].forEach(option => option.addEventListener('click', handleClickOnOption, false));
    });
})(window, document);
