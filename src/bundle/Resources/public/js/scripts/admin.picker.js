(function (global, doc, flatpickr) {
    const SELECTOR_PICKER = '.ez-picker';
    const SELECTOR_PICKER_INPUT = '.ez-picker__input';
    const SELECTOR_FORM_INPUT = '.ez-picker__form-input';
    const SELECTOR_CLEAR_BTN = '.ez-picker__btn--clear-input';
    const pickers = [...doc.querySelectorAll(SELECTOR_PICKER)];
    const pickerConfig = {
        enableTime: true,
        time_24hr: true,
        formatDate: (date) => (new Date(date)).toLocaleString(),
    };
    const updateInputValue = (formInput, date) => {
        if (!date.length) {
            formInput.value = '';

            return;
        }

        date = new Date(date[0]);
        formInput.value = Math.floor(date.getTime() / 1000);
    };
    const initFlatPickr = (field) => {
        const formInput = field.querySelector(SELECTOR_FORM_INPUT);
        const pickerInput = field.querySelector(SELECTOR_PICKER_INPUT);
        const btnClear = field.querySelector(SELECTOR_CLEAR_BTN);
        let defaultDate;

        if (formInput.value) {
            defaultDate = new Date(formInput.value * 1000);
        }

        btnClear.addEventListener('click', (event) => {
            event.preventDefault();

            pickerInput.value = '';
            formInput.value = '';
        }, false);

        flatpickr(pickerInput, Object.assign({}, pickerConfig, {
            onChange: updateInputValue.bind(null, formInput),
            defaultDate,
        }));
    };

    pickers.forEach(initFlatPickr);
})(window, window.document, window.flatpickr);
