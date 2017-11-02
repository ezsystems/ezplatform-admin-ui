(function () {
    const createActions = document.querySelector('.ez-extra-actions--create');
    const btns = [...createActions.querySelectorAll('.radio [type="radio"]')];
    const form = createActions.querySelector('form');

    btns.forEach(btn => btn.addEventListener('change', () => form.submit(), false));
})();
