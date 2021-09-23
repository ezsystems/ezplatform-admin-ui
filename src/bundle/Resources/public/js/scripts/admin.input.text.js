(function (global, doc, $, eZ) {
    const textInputClearBtns = doc.querySelectorAll('.ibexa-input-text-wrapper__action-btn--clear');
    const clearText = (event) => {
        const inputWrapper = event.target.closest('.ibexa-input-text-wrapper');
        const textInput = inputWrapper.querySelector('.ibexa-input--text');

        textInput.value = '';
        textInput.dispatchEvent(new Event('input'));
        textInput.select();
    };

    textInputClearBtns.forEach((clearBtn) => clearBtn.addEventListener('click', clearText, false));

    doc.body.addEventListener(
        'ibexa-drop-field-definition',
        (event) => {
            const { nodes } = event.detail;

            nodes.forEach((node) => {
                const fieldTextInputClearBtns = node.querySelectorAll('.ibexa-input-text-wrapper__clear-btn');

                fieldTextInputClearBtns.forEach((clearBtn) => clearBtn.addEventListener('click', clearText, false));
            });
        },
        false
    );
})(window, window.document, window.jQuery, window.eZ);
