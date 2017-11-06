(function () {
    const editActions = document.querySelector('.ez-extra-actions--edit');
    const btns = [...editActions.querySelectorAll('.radio [type="radio"]')];
    const form = editActions.querySelector('form');

    btns.forEach(btn => btn.addEventListener('change', () => form.submit(), false));
})();
