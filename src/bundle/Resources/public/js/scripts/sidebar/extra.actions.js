(function () {
    const CLASS_HIDDEN = 'ez-extra-actions--hidden';
    const CLASS_PREVENT_SHOW = 'ez-extra-actions--prevent-show';
    const btns = [...document.querySelectorAll('.ez-btn--extra-actions')];

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const actions = document.querySelector(`.ez-extra-actions[data-actions="${btn.dataset.actions}"]`);
            const haveHiddenPart = (element) => {
                return element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW)
            };
            const methodName = haveHiddenPart(actions) ? 'remove' : 'add';
            const clickOutsideMethodName = actions.classList.contains(CLASS_HIDDEN) ? 'addEventListener' : 'removeEventListener';
            const focusElement = actions.querySelector(btn.dataset.focusElement);
            const detectClickOutside = (event) => {
                const isNotButton = !event.target.contains(btn);
                const isNotExtraActions = !event.target.closest('.ez-extra-actions');
                const isNotCalendar = !event.target.closest('.flatpickr-calendar');

                if (isNotButton && isNotExtraActions && isNotCalendar) {
                    actions.classList.add(CLASS_HIDDEN);
                    document.body.removeEventListener('click', detectClickOutside, false);
                }
            };

            actions.style.top = btn.offsetTop + 'px';
            actions.classList[methodName](CLASS_HIDDEN);
            document.body[clickOutsideMethodName]('click', detectClickOutside, false);

            if (focusElement) {
                focusElement.focus();
            }
        }, false);
    });
})();
