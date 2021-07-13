(function (global, doc, $, eZ) {
    const textInputClearBtns = doc.querySelectorAll('.ibexa-input-text-wrapper__clear-btn');
    const textInputsInvalid = doc.querySelectorAll('.ibexa-input--text.is-invalid');
    const textInputsInvalidOriginalValuesMap = new Map();
    const clearText = (event) => {
        const inputWrapper = event.target.closest('.ibexa-input-text-wrapper');
        const textInput = inputWrapper.querySelector('.ibexa-input--text');

        textInput.value = '';
        textInput.dispatchEvent(new Event('input'));
        textInput.select();
    };
    const handleInputChange = (event) => {
        const textInput = event.target;
        const isValueDifferentFromOriginal = textInput.value !== textInputsInvalidOriginalValuesMap.get(textInput);

        textInput.classList.toggle('is-invalid', !isValueDifferentFromOriginal);
    };

    textInputsInvalid.forEach((textInputInvalid) => textInputsInvalidOriginalValuesMap.set(textInputInvalid, textInputInvalid.value));
    textInputsInvalid.forEach((textInput) => textInput.addEventListener('input', handleInputChange, false));
    textInputClearBtns.forEach((clearBtn) => clearBtn.addEventListener('click', clearText, false));
})(window, window.document, window.jQuery, window.eZ);
