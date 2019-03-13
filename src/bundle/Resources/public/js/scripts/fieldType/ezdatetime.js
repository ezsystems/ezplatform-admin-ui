(function(global, doc, eZ, moment, flatpickr) {
    const SELECTOR_FIELD = '.ez-field-edit--ezdatetime';
    const SELECTOR_INPUT = '.ez-data-source__input[data-seconds]';
    const SELECTOR_FLATPICKR_INPUT = '.flatpickr-input';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const EVENT_VALUE_CHANGED = 'valueChanged';

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

    const validator = new EzDateTimeValidator({
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

    const datetimeFields = [...doc.querySelectorAll(SELECTOR_FIELD)];
    const datetimeConfig = {
        enableTime: true,
        time_24hr: true,
        formatDate: (date) => new Date(date).toLocaleString(),
    };
    const updateInputValue = (sourceInput, dates) => {
        const event = new CustomEvent(EVENT_VALUE_CHANGED);

        if (!dates.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        const selectedDate = moment(dates[0]);
        const selectedDateWithUserTimezone = selectedDate.tz(eZ.adminUiConfig.timezone, true);
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
        const btnClear = field.querySelector('.ez-data-source__btn--clear-input');
        let defaultDate = null;

        if (sourceInput.value) {
            const defaultDateWithUserTimezone = eZ.helpers.timezone.convertDateToTimezone(sourceInput.value * 1000);
            const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            defaultDate = new Date(defaultDateWithUserTimezone.tz(browserTimezone, true));
        }

        const flatpickrInstance = flatpickr(
            flatPickrInput,
            Object.assign({}, datetimeConfig, {
                onChange: updateInputValue.bind(null, sourceInput),
                defaultDate,
                enableSeconds: !!parseInt(sourceInput.dataset.seconds, 10),
            })
        );

        btnClear.addEventListener('click', clearValue.bind(null, sourceInput, flatpickrInstance), false);

        if (sourceInput.hasAttribute('required')) {
            flatPickrInput.setAttribute('required', true);
        }
    };

    datetimeFields.forEach(initFlatPickr);
})(window, window.document, window.eZ, window.moment, window.flatpickr);
