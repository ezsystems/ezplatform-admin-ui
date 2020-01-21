(function(global, doc, eZ, flatpickr) {
    const SELECTOR_FIELD = '.ez-field-edit--ezdate';
    const SELECTOR_INPUT = '.ez-data-source__input:not(.flatpickr-input)';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const SELECTOR_FLATPICKR_INPUT = '.flatpickr-input';
    const EVENT_VALUE_CHANGED = 'valueChanged';

    class EzDateValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzDateValidator
         */
        validateInput(event) {
            const target = event.currentTarget;
            const isRequired = target.required;
            const isEmpty = !target.value.trim().length;
            const label = event.target.closest(this.fieldSelector).querySelector('.ez-field-edit__label').innerHTML;
            let isError = false;
            let errorMessage = '';

            if (isRequired && isEmpty) {
                isError = true;
                errorMessage = eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return {
                isError,
                errorMessage,
            };
        }
    }

    const validator = new EzDateValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_INPUT}`,
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                invalidStateSelectors: [SELECTOR_FLATPICKR_INPUT],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FLATPICKR_INPUT}`,
                eventName: 'blur',
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_LABEL_WRAPPER],
                invalidStateSelectors: [SELECTOR_FLATPICKR_INPUT],
            },
        ],
    });

    validator.init();

    eZ.fieldTypeValidators = eZ.fieldTypeValidators ? [...eZ.fieldTypeValidators, validator] : [validator];

    const dateFields = [...doc.querySelectorAll(SELECTOR_FIELD)];
    const dateConfig = {
        formatDate: (date) => eZ.helpers.timezone.formatFullDateTime(date, null, eZ.adminUiConfig.dateFormat.fullDate),
    };
    const updateInputValue = (sourceInput, date) => {
        const event = new CustomEvent(EVENT_VALUE_CHANGED);

        if (!date.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        date = new Date(date[0]);
        date = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));

        sourceInput.value = Math.floor(date.getTime() / 1000);
        sourceInput.dispatchEvent(event);
    };
    const clearValue = (sourceInput, flatpickrInstance, event) => {
        event.preventDefault();

        flatpickrInstance.clear();

        sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
    };
    const initFlatPickr = (field) => {
        const sourceInput = field.querySelector(SELECTOR_INPUT);
        const flatPickrInput = field.querySelector(SELECTOR_FLATPICKR_INPUT);
        const btnClear = field.querySelector('.ez-data-source__btn--clear-input');
        let defaultDate = null;

        if (sourceInput.value) {
            defaultDate = new Date(sourceInput.value * 1000);
            const timezoneOffset = sourceInput.dataset.timezoneOffset
                ? sourceInput.dataset.timezoneOffset
                : defaultDate.getTimezoneOffset() * 60;

            defaultDate.setTime(defaultDate.getTime() + timezoneOffset * 1000);

            updateInputValue(sourceInput, [defaultDate]);
        }

        const flatpickrInstance = flatpickr(
            flatPickrInput,
            Object.assign({}, dateConfig, {
                onChange: updateInputValue.bind(null, sourceInput),
                defaultDate,
            })
        );

        btnClear.addEventListener('click', clearValue.bind(null, sourceInput, flatpickrInstance), false);

        if (sourceInput.hasAttribute('required')) {
            flatPickrInput.setAttribute('required', true);
        }
    };

    dateFields.forEach(initFlatPickr);
})(window, window.document, window.eZ, window.flatpickr);
