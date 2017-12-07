(function () {
    const checkboxes = [...document.querySelectorAll('form[name="delete-roles"] input[type="checkbox"]')];
    const buttonRemove = document.querySelector('#delete-roles');

    const toggleButtonState = (event) => {
        const methodName = checkboxes.some(el => el.checked) ? 'removeAttribute' : 'setAttribute';
        buttonRemove[methodName]('disabled', true);
    }

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', toggleButtonState, false));
})();
