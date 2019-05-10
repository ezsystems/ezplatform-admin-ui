(function(global, doc) {
    const modal = doc.querySelector('#ez-modal--custom-url-alias');

    if (modal) {
        const discardBtns = modal.querySelectorAll('[data-dismiss="modal"]');
        const submitBtn = modal.querySelector('[type="submit"]');
        const input = modal.querySelector('[required="required"]');
        const checkboxes = modal.querySelectorAll('.ez-field-edit--ezboolean input');
        const toggleButtonState = () => {
            const hasValue = input.value.trim().length !== 0;
            const methodName = hasValue ? 'removeAttribute' : 'setAttribute';

            submitBtn[methodName]('disabled', true);
        };
        const toggleCheckbox = (event) => {
            const checkbox = event.target;
            const methodName = checkbox.checked ? 'add' : 'remove';

            checkbox.closest('.ez-data-source__label').classList[methodName]('is-checked');
        };
        const clearValues = () => {
            input.value = '';
            toggleButtonState();
        };

        input.addEventListener('input', toggleButtonState, false);
        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', toggleCheckbox, false));
        discardBtns.forEach((btn) => btn.addEventListener('click', clearValues, false));
    }
})(window, window.document);
