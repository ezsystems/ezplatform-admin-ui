(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--eztime';
    const SELECTOR_INPUT = '.ez-data-source__input:not(.flatpickr-input)';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const SELECTOR_FLATPICKR_INPUT = '.flatpickr-input';
    const EVENT_VALUE_CHANGED = 'valueChanged';

    class EzTimeValidator extends global.eZ.BaseFieldValidator {
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
            const label = event.target.closest(this.fieldSelector).querySelector('.ez-field-edit__label').innerHTML;
            let isError = false;
            let errorMessage = '';

            if (isRequired && isEmpty) {
                isError = true;
                errorMessage = window.eZ.errors.emptyField.replace('{fieldName}', label);
            }

            return {
                isError,
                errorMessage
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

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];

    const timeFields = [...document.querySelectorAll(SELECTOR_FIELD)];
    const timeConfig = {
        enableTime: true,
        noCalendar: true,
        time_24hr: true
    };
    const updateInputValue = (sourceInput, date) => {
        const event = new CustomEvent(EVENT_VALUE_CHANGED);

        if (!date.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        date = new Date(date[0]);
        sourceInput.value = (date.getHours() * 3600) + (date.getMinutes() * 60) + date.getSeconds();

        sourceInput.dispatchEvent(event);
    };
    const initFlatPickr = (field) => {
        const sourceInput = field.querySelector(SELECTOR_INPUT);
        const flatPickrInput = field.querySelector(SELECTOR_FLATPICKR_INPUT);
        const btnClear = field.querySelector('.ez-data-source__btn--clear-input');
        const enableSeconds = sourceInput.dataset.seconds === '1';
        let defaultDate;

        if (sourceInput.value) {
            const value = parseInt(sourceInput.value, 10);
            const date = new Date();

            date.setHours(Math.floor(value / 3600));
            date.setMinutes(Math.floor(value % 3600 / 60));
            date.setSeconds(Math.floor(value % 3600 % 60));

            defaultDate = date;
        }

        btnClear.addEventListener('click', (event) => {
            event.preventDefault();

            flatPickrInput.value = '';
            sourceInput.value = '';

            sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
        }, false);

        window.flatpickr(flatPickrInput, Object.assign({}, timeConfig, {
            enableSeconds,
            onChange: updateInputValue.bind(null, sourceInput),
            dateFormat: enableSeconds ? 'H:i:S' : 'H:i',
            defaultDate
        }));

        if (sourceInput.hasAttribute('required')) {
            flatPickrInput.setAttribute('required', true);
        }
    };

    timeFields.forEach(initFlatPickr);
})(window);
