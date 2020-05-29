(function (global, doc) {
    const modal = doc.querySelector('#create-wildcards-modal');

    if (!modal) {
        return;
    }

    const discardBtns = modal.querySelectorAll('[data-dismiss="modal"]');
    const submitBtn = modal.querySelector('[type="submit"]');
    const inputs = [...modal.querySelectorAll('[required="required"]')];
    const checkboxes = modal.querySelectorAll('.ez-field-edit--ezboolean input');
    const toggleButtonState = () => {
        const isInvalid = inputs.some((input) => input.value.trim().length === 0);
        const methodName = isInvalid ? 'setAttribute' : 'removeAttribute';

        submitBtn[methodName]('disabled', true);
    };
    const toggleCheckbox = (event) => {
        const checkbox = event.target;

        checkbox.closest('.ez-data-source__label').classList.toggle('is-checked', checkbox.checked);
    };
    const clearValues = () => {
        inputs.forEach((input) => {
            input.value = '';
        });
        toggleButtonState();
    };

    inputs.forEach((input) => input.addEventListener('input', toggleButtonState, false));
    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', toggleCheckbox, false));
    discardBtns.forEach((btn) => btn.addEventListener('click', clearValues, false));
})(window, window.document);
