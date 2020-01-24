(function(global, doc) {
    const CLASS_HIDDEN = 'ez-extra-actions--hidden';
    const CLASS_ACTIVE_BUTTON = 'ez-btn--active-button';
    const CLASS_PREVENT_SHOW = 'ez-extra-actions--prevent-show';
    const btns = doc.querySelectorAll('.ez-btn--extra-actions');
    const haveHiddenPart = (element) => element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW);

    btns.forEach((btn) => {
        btn.addEventListener(
            'click',
            () => {
                const actions = doc.querySelector(`.ez-extra-actions[data-actions="${btn.dataset.actions}"]`);

                const methodNameButton = haveHiddenPart(actions) ? 'add' : 'remove';
                const methodNameContainer = haveHiddenPart(actions) ? 'remove' : 'add';
                const focusElement = actions.querySelector(btn.dataset.focusElement);
                const detectClickOutside = (event) => {
                    const isNotButton = event.target !== btn || !btn.contains(event.target);
                    const isNotExtraActions = !event.target.closest('.ez-extra-actions');
                    const isNotCalendar = !event.target.closest('.flatpickr-calendar');

                    if (isNotButton && isNotExtraActions && isNotCalendar) {
                        btn.classList.remove(CLASS_ACTIVE_BUTTON);
                        actions.classList.add(CLASS_HIDDEN);
                        doc.body.removeEventListener('click', detectClickOutside, false);
                    }
                };

                btn.classList[methodNameButton](CLASS_ACTIVE_BUTTON);
                actions.classList[methodNameContainer](CLASS_HIDDEN);

                if (!actions.classList.contains(CLASS_HIDDEN)) {
                    doc.body.addEventListener('click', detectClickOutside, false);
                } else {
                    doc.body.removeEventListener('click', detectClickOutside);
                }

                if (focusElement) {
                    focusElement.focus();
                }
            },
            false
        );
    });
})(window, window.document);
