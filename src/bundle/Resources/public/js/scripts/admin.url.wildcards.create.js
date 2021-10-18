(function(global, doc) {
    const modal = doc.querySelector('#create-wildcards-modal');

    if (!modal) {
        return;
    }

    const discardBtns = modal.querySelectorAll('[data-bs-dismiss="modal"]');
    const submitBtn = modal.querySelector('[type="submit"]');
    const inputs = [...modal.querySelectorAll('[required="required"]')];
    const toggleButtonState = () => {
        const isInvalid = inputs.some((input) => input.value.trim().length === 0);
        const methodName = isInvalid ? 'setAttribute' : 'removeAttribute';

        submitBtn[methodName]('disabled', true);
    };
    const clearValues = () => {
        inputs.forEach((input) => {
            input.value = '';
        });
        toggleButtonState();
    };

    inputs.forEach((input) => input.addEventListener('input', toggleButtonState, false));
    discardBtns.forEach((btn) => btn.addEventListener('click', clearValues, false));
})(window, window.document);
