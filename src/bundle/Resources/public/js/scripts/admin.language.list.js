(function () {
    const checkboxes = [...document.querySelectorAll('form[name="languages_delete"] input[type="checkbox"]')];
    const buttonRemove = document.querySelector('#delete-languages');
    const toggleButtonState = (event) => {
        const methodName = checkboxes.some(el => el.checked) ? 'removeAttribute' : 'setAttribute';
        buttonRemove[methodName]('disabled', true);
    }

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', toggleButtonState, false));
})();
