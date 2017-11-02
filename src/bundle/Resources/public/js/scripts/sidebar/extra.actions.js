(function () {
    const CLASS_HIDDEN = 'ez-extra-actions--hidden';
    const btns = [...document.querySelectorAll('.ez-btn--extra-actions')];

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const actions = document.querySelector(`.ez-extra-actions[data-actions="${btn.dataset.actions}"]`);
            const cssMethodName = actions.classList.contains(CLASS_HIDDEN) ? 'remove' : 'add';
            const clickOutsideMethodName = actions.classList.contains(CLASS_HIDDEN) ? 'addEventListener' : 'removeEventListener';
            const btnRect = btn.getBoundingClientRect();
            const focusElement = actions.querySelector(btn.dataset.focusElement);
            const detectClickOutside = (event) => {
                const isNotButton = !event.target.contains(btn);
                const isNotExtraActions = !event.target.closest('.ez-extra-actions');

                if (isNotButton && isNotExtraActions) {
                    actions.classList.add(CLASS_HIDDEN);
                    document.body.removeEventListener('click', detectClickOutside, false);
                }
            };

            actions.style.top = btn.offsetTop + 'px';
            actions.classList[cssMethodName](CLASS_HIDDEN);
            document.body[clickOutsideMethodName]('click', detectClickOutside, false);

            if (focusElement) {
                focusElement.focus();
            }
        }, false);
    });
})();
