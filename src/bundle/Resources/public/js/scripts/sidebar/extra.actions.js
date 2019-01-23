(function (global, doc) {
    const CLASS_HIDDEN = 'ez-extra-actions--hidden';
    const CLASS_PREVENT_SHOW = 'ez-extra-actions--prevent-show';
    const btns = doc.querySelectorAll('.ez-btn--extra-actions');
    const haveHiddenPart = (element) => element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW);

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const actions = doc.querySelector(`.ez-extra-actions[data-actions="${btn.dataset.actions}"]`);

            const methodName = haveHiddenPart(actions) ? 'remove' : 'add';
            const focusElement = actions.querySelector(btn.dataset.focusElement);
            const detectClickOutside = (event) => {
                const isNotButton = event.target !== btn || !btn.contains(event.target);
                const isNotExtraActions = !event.target.closest('.ez-extra-actions');
                const isNotCalendar = !event.target.closest('.flatpickr-calendar');

                if (isNotButton && isNotExtraActions && isNotCalendar) {
                    actions.classList.add(CLASS_HIDDEN);
                    doc.body.removeEventListener('click', detectClickOutside, false);
                }
            };

            actions.classList[methodName](CLASS_HIDDEN);

            const actionsRect = actions.getBoundingClientRect();

            actions.style.opacity = 0;

            const fitsViewport = actionsRect.height + btn.offsetTop <= global.innerHeight;

            if (!fitsViewport) {
                actions.style.bottom = `0px`;
                actions.style.top = 'auto';
            } else {
                actions.style.top = `${btn.offsetTop}px`;
                actions.style.bottom = 'auto';
            }

            if (!actions.classList.contains(CLASS_HIDDEN)) {
                doc.body.addEventListener('click', detectClickOutside, false);
            } else {
                doc.body.removeEventListener('click', detectClickOutside);
            }

            if (focusElement) {
                focusElement.focus();
            }

            actions.style.opacity = 1;
        }, false);
    });
})(window, window.document);
