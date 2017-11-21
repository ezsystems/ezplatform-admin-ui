(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezdatetime';
    const SELECTOR_INPUT = '.ez-data-source__input[data-seconds]';
    const EVENT_VALUE_CHANGED = 'valueChanged';

    class EzDateTimeValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzDateTimeValidator
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

    const validator = new EzDateTimeValidator({
        classInvalid: 'is-invalid',
        fieldSelector: SELECTOR_FIELD,
        eventsMap: [
            {
                selector: `${SELECTOR_FIELD} ${SELECTOR_INPUT}`,
                eventName: EVENT_VALUE_CHANGED,
                callback: 'validateInput',
                errorNodeSelectors: ['.ez-field-edit__label-wrapper'],
            },
        ],
    });

    validator.init();

    global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        [...global.eZ.fieldTypeValidators, validator] :
        [validator];

    const datetimeFields = [...document.querySelectorAll(SELECTOR_FIELD)];
    const datetimeConfig = {
        enableTime: true,
        time_24hr: true,
        formatDate: (date) => (new Date(date)).toLocaleString()
    };
    const updateInputValue = (sourceInput, date) => {
        const event = new CustomEvent(EVENT_VALUE_CHANGED);

        if (!date.length) {
            sourceInput.value = '';
            sourceInput.dispatchEvent(event);

            return;
        }

        date = new Date(date[0]);
        sourceInput.value = Math.floor(date.getTime() / 1000);

        sourceInput.dispatchEvent(event);
    };
    const initFlatPickr = (field) => {
        const sourceInput = field.querySelector(SELECTOR_INPUT);
        const flatPickrInput = field.querySelector('.flatpickr-input');
        const btnClear = field.querySelector('.ez-data-source__btn--clear-input');
        let defaultDate;

        if (sourceInput.value) {
            defaultDate = new Date(sourceInput.value * 1000);
        }

        btnClear.addEventListener('click', (event) => {
            event.preventDefault();

            flatPickrInput.value = '';
            sourceInput.value = '';
        }, false);

        flatpickr(flatPickrInput, Object.assign({}, datetimeConfig, {
            onChange: updateInputValue.bind(null, sourceInput),
            defaultDate,
            enableSeconds: !!parseInt(sourceInput.dataset.seconds, 10),
        }));
    };

    datetimeFields.forEach(initFlatPickr);
})(window);
