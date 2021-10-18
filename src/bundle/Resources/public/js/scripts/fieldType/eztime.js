(function(global, doc, eZ, flatpickr) {
    const SELECTOR_FIELD = '.ibexa-field-edit--eztime';
    const SELECTOR_INPUT = '.ibexa-data-source__input:not(.flatpickr-input)';
    const SELECTOR_FLATPICKR_INPUT = '.flatpickr-input';
    const SELECTOR_ERROR_NODE = '.ibexa-data-source';
    const EVENT_VALUE_CHANGED = 'change';

    class EzTimeValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzTimeValidator
         */
        validateInput(event) {
            const target = event.currentTarget;
            const isRequired = target.required;
            const isEmpty = !target.value.trim().length;
            const label = event.target.closest(this.fieldSelector).querySelector('.ibexa-field-edit__label').innerHTML;
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

    const validator = new EzTimeValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_INPUT}`,
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
                invalidStateSelectors: [SELECTOR_FLATPICKR_INPUT],
            },
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_FLATPICKR_INPUT}`,
                eventName: 'blur',
                callback: 'validateInput',
                errorNodeSelectors: [SELECTOR_ERROR_NODE],
                invalidStateSelectors: [SELECTOR_FLATPICKR_INPUT],
            },
        ],
    });

    validator.init();

    eZ.addConfig('fieldTypeValidators', [validator], true);

    const timeFields = doc.querySelectorAll(SELECTOR_FIELD);
    const timeConfig = {
        enableTime: true,
        noCalendar: true,
        time_24hr: true,
        formatDate: (date) => eZ.helpers.timezone.formatFullDateTime(date, null, eZ.adminUiConfig.dateFormat.fullTime),
    };
    const updateInputValue = (sourceInput, date) => {
        const event = new CustomEvent(EVENT_VALUE_CHANGED);

        if (!date.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        date = new Date(date[0]);
        sourceInput.value = date.getHours() * 3600 + date.getMinutes() * 60 + date.getSeconds();

        sourceInput.dispatchEvent(event);
    };
    const initFlatPickr = (field) => {
        const sourceInput = field.querySelector(SELECTOR_INPUT);
        const flatPickrInput = field.querySelector(SELECTOR_FLATPICKR_INPUT);
        const btnClear = field.querySelector('.ibexa-data-source__btn--clear-input');
        const enableSeconds = sourceInput.dataset.seconds === '1';
        let defaultDate;

        if (sourceInput.value) {
            const value = parseInt(sourceInput.value, 10);
            const date = new Date();

            date.setHours(Math.floor(value / 3600));
            date.setMinutes(Math.floor((value % 3600) / 60));
            date.setSeconds(Math.floor((value % 3600) % 60));

            defaultDate = date;
        }

        btnClear.addEventListener(
            'click',
            (event) => {
                event.preventDefault();

                flatPickrInput.value = '';
                sourceInput.value = '';

                sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
            },
            false
        );

        flatpickr(flatPickrInput, {
            ...timeConfig,
            enableSeconds,
            onChange: updateInputValue.bind(null, sourceInput),
            onClose: updateInputValue.bind(null, sourceInput),
            defaultDate,
        });

        if (sourceInput.hasAttribute('required')) {
            flatPickrInput.setAttribute('required', true);
        }
    };

    timeFields.forEach(initFlatPickr);
})(window, window.document, window.eZ, window.flatpickr);
