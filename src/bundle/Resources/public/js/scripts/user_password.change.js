(function(global, doc, eZ) {
    const form = doc.querySelector('form[name="user_password_change"]');
    const submitBtns = form.querySelectorAll('[type="submit"]:not([formnovalidate])');
    const oldPasswordInput = form.querySelector('#user_password_change_oldPassword');
    const newPasswordInput = form.querySelector('#user_password_change_newPassword_first');
    const confirmPasswordInput = form.querySelector('#user_password_change_newPassword_second');
    const SELECTOR_FIELD = '.ibexa-field';
    const SELECTOR_LABEL = '.ibexa-field__label';
    const CLASS_INVALID = 'is-invalid';

    /**
     * Creates an error node
     *
     * @method createErrorNode
     * @param {String} message
     * @returns {HTMLElement}
     */
    const createErrorNode = (message) => {
        const errorNode = doc.createElement('em');

        errorNode.classList.add('ibexa-field__error');
        errorNode.innerHTML = message;

        return errorNode;
    };

    /**
     * Toggles the error
     *
     * @method toggleError
     * @param {Boolean} isError
     * @param {String} message
     * @param {HTMLElement} target
     */
    const toggleError = (isError, message, target) => {
        const methodName = isError ? 'add' : 'remove';
        const field = target.closest(SELECTOR_FIELD);
        const labelWrapper = field.querySelector('.ibexa-form-error');
        const errorNodes = labelWrapper.querySelectorAll('.ibexa-field__error');

        field.classList[methodName](CLASS_INVALID);
        target.classList[methodName](CLASS_INVALID);

        errorNodes.forEach((el) => el.remove());

        if (isError) {
            labelWrapper.append(createErrorNode(message));
        }
    };

    /**
     * Compares passwords
     *
     * @method comparePasswords
     * @return {Boolean}
     */
    const comparePasswords = () => {
        const newPassword = newPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        const isNotEmptyPassword = checkIsNotEmpty(newPasswordInput) && checkIsNotEmpty(confirmPasswordInput);
        const passwordMatch = newPassword === confirmPassword;
        const message = eZ.errors.notSamePasswords;

        if (!passwordMatch) {
            toggleError(!passwordMatch, message, confirmPasswordInput);
        }

        return passwordMatch && isNotEmptyPassword;
    };

    /**
     * Checks if input has not empty value
     *
     * @method checkIsNotEmpty
     * @param {HTMLElement} target
     * @return {Boolean}
     */
    const checkIsNotEmpty = (target) => {
        const isRequired = target.required;
        const isEmpty = !target.value.trim();
        const isError = isRequired && isEmpty;
        const fieldContainer = target.closest(SELECTOR_FIELD);
        const message = eZ.errors.emptyField.replace('{fieldName}', fieldContainer.querySelector(SELECTOR_LABEL).innerHTML);

        toggleError(isError, message, target);

        return !isError;
    };

    form.setAttribute('novalidate', true);

    submitBtns.forEach((btn) => {
        const clickHandler = (event) => {
            if (!parseInt(btn.dataset.isFormValid, 10)) {
                event.preventDefault();

                const requiredFields = [...form.querySelectorAll('.ez-field input[required]')];
                const isFormValid = requiredFields.map(checkIsNotEmpty).every((result) => result) && comparePasswords();

                if (isFormValid) {
                    btn.dataset.isFormValid = 1;
                    // for some reason trying to fire click event inside the event handler flow is impossible
                    // the following line breaks the flow so it's possible to fire click event on a button again.
                    global.setTimeout(() => btn.click(), 0);
                }
            }
        };

        btn.dataset.isFormValid = 0;
        btn.addEventListener('click', clickHandler, false);
    });

    oldPasswordInput.addEventListener('blur', (event) => checkIsNotEmpty(event.currentTarget), false);
    newPasswordInput.addEventListener('blur', (event) => checkIsNotEmpty(event.currentTarget), false);
    confirmPasswordInput.addEventListener('blur', (event) => checkIsNotEmpty(event.currentTarget), false);
    confirmPasswordInput.addEventListener('blur', comparePasswords, false);
})(window, window.document, window.eZ);
