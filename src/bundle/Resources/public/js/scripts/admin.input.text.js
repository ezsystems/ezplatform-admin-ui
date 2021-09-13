(function(global, doc) {
    const textInputClearBtns = doc.querySelectorAll('.ibexa-input-text-wrapper__clear-btn');
    const clearText = (event) => {
        const inputWrapper = event.target.closest('.ibexa-input-text-wrapper');
        const textInput = inputWrapper.querySelector('.ibexa-input--text');

        textInput.value = '';
        textInput.dispatchEvent(new Event('input'));
        textInput.select();
    };

    textInputClearBtns.forEach((clearBtn) => clearBtn.addEventListener('click', clearText, false));
})(window, window.document);
