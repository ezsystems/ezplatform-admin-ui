(function(global, doc, eZ, flatpickr) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ezdatetime';
    const SELECTOR_INPUT = '.ibexa-data-source__input[data-seconds]';
    const SELECTOR_FLATPICKR_INPUT = '.flatpickr-input';
    const EVENT_VALUE_CHANGED = 'change';
    const SELECTOR_ERROR_NODE = '.ibexa-data-source';
    const { convertDateToTimezone, formatShortDateTime } = eZ.helpers.timezone;
    const userTimezone = eZ.adminUiConfig.timezone;

    class EzDateTimeValidator extends eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzDateTimeValidator
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

    const validator = new EzDateTimeValidator({
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

    const datetimeFields = doc.querySelectorAll(SELECTOR_FIELD);
    const datetimeConfig = {
        enableTime: true,
        time_24hr: true,
        formatDate: (date) => formatShortDateTime(date, null),
    };
    const updateInputValue = (sourceInput, dates) => {
        const event = new CustomEvent(EVENT_VALUE_CHANGED);

        if (!dates.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        const selectedDate = dates[0];
        const selectedDateWithUserTimezone = convertDateToTimezone(selectedDate, userTimezone, true);
        const timestamp = Math.floor(selectedDateWithUserTimezone.valueOf() / 1000);

        sourceInput.value = timestamp;
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
        const btnClear = field.querySelector('.ibexa-data-source__btn--clear-input');
        const secondsEnabled = sourceInput.dataset.seconds === '1';
        let defaultDate = null;

        if (sourceInput.value) {
            const defaultDateWithUserTimezone = convertDateToTimezone(sourceInput.value * 1000);
            const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            defaultDate = new Date(convertDateToTimezone(defaultDateWithUserTimezone, browserTimezone, true));
        }

        const flatpickrInstance = flatpickr(flatPickrInput, {
            ...datetimeConfig,
            onChange: updateInputValue.bind(null, sourceInput),
            defaultDate,
            enableSeconds: secondsEnabled,
        });

        btnClear.addEventListener('click', clearValue.bind(null, sourceInput, flatpickrInstance), false);

        if (sourceInput.hasAttribute('required')) {
            flatPickrInput.setAttribute('required', true);
        }
    };

    datetimeFields.forEach(initFlatPickr);
})(window, window.document, window.eZ, window.flatpickr);
