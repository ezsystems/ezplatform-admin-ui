(function () {
    const editActions = document.querySelector('.ez-extra-actions--edit');
    const btns = [...editActions.querySelectorAll('.form-check [type="radio"]')];
    const form = editActions.querySelector('form');

    btns.forEach(btn => btn.addEventListener('change', () => form.submit(), false));
})();
