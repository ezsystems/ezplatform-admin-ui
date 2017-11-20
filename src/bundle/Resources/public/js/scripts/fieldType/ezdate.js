(function (global) {
    const SELECTOR_FIELD = '.ez-field-edit--ezdate';
    const SELECTOR_INPUT = '.ez-data-source__input:not(.flatpickr-input)';
    const EVENT_VALUE_CHANGED = 'valueChanged';

    class EzDateValidator extends global.eZ.BaseFieldValidator {
        /**
         * Validates the input
         *
         * @method validateInput
         * @param {Event} event
         * @returns {Object}
         * @memberof EzDateValidator
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
    }

    const validator = new EzDateValidator({
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

    const dateFields = [...document.querySelectorAll(SELECTOR_FIELD)];
    const dateConfig = {
        formatDate: (date) => (new Date(date)).toLocaleDateString()
    };
    const updateInputValue = (sourceInput, date) => {
        date = new Date(date);
        date = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));

        sourceInput.value = Math.floor(date.getTime() / 1000);
        sourceInput.dispatchEvent(new CustomEvent(EVENT_VALUE_CHANGED));
    };
    const initFlatPickr = (field) => {
        const sourceInput = field.querySelector(SELECTOR_INPUT);
        let defaultDate;

        if (sourceInput.value) {
            defaultDate = new Date(sourceInput.value * 1000);
        }

        sourceInput.classList.add('ez-data-source__input--visually-hidden');

        flatpickr(field.querySelector('.flatpickr-input'), Object.assign({}, dateConfig, {
            onChange: updateInputValue.bind(null, sourceInput),
            defaultDate
        }));
    };

    dateFields.forEach(initFlatPickr);
})(window);
