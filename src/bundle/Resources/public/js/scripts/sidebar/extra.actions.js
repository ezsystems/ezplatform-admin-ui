(function () {
    const CLASS_HIDDEN = 'ez-extra-actions--hidden';
    const btns = [...document.querySelectorAll('.ez-btn--extra-actions')];

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const actions = document.querySelector(`.ez-extra-actions[data-actions="${btn.dataset.actions}"]`);
            const cssMethodName = actions.classList.contains(CLASS_HIDDEN) ? 'remove' : 'add';
            const clickOutsideMethodName = actions.classList.contains(CLASS_HIDDEN) ? 'addEventListener' : 'removeEventListener';
            const btnRect = btn.getBoundingClientRect();
            const detectClickOutside = (event) => {
                if (!event.target.classList.contains('ez-extra-actions') && !event.target.contains(actions) && !event.target.contains(btn)) {
                    actions.classList.add(CLASS_HIDDEN);
                    document.body.removeEventListener('click', detectClickOutside, false);
                }
            };

            actions.style.top = btn.offsetTop + (btnRect.height / 2) + 'px';
            actions.classList[cssMethodName](CLASS_HIDDEN);
            document.body[clickOutsideMethodName]('click', detectClickOutside, false);
        }, false);
    });
})();
