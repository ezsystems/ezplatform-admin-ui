(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--eztime';
    const SELECTOR_INPUT = '.ez-data-source__input:not(.flatpickr-input)';
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
            const target = event.target;
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
    };

    const validator = new EzTimeValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_INPUT}`,
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                invalidStateSelectors: [SELECTOR_FIELD],
                errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
            },
        ],
    });

    validator.init();

    const timeFields = [...document.querySelectorAll(SELECTOR_FIELD)];
    const timeConfig = {
        enableTime: true,
        noCalendar: true,
        time_24hr: true
    };
    const updateInputValue = (sourceInput, date) => {
        sourceInput.value = Math.floor((new Date(date)).getTime() / 1000);
        sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
    };
    const initFlatPickr = (field) => {
        const sourceInput = field.querySelector(SELECTOR_INPUT);
        const enableSeconds = sourceInput.dataset.seconds === '1';
        let defaultDate;

        sourceInput.classList.add('ez-data-source__input--visually-hidden');

        if (sourceInput.value) {
            const date = new Date(sourceInput * 1000);

            if (isNaN(date.getTime())) {
                sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
                sourceInput.value = '';
            } else {
                defaultDate = date;
            }
        }

        flatpickr(field.querySelector('.flatpickr-input'), Object.assign({}, timeConfig, {
            enableSeconds,
            onChange: updateInputValue.bind(null, sourceInput),
            dateFormat: enableSeconds ? 'H:i:S' : 'H:i',
            defaultDate
        }));
    };

    timeFields.forEach(initFlatPickr);
})(window);
