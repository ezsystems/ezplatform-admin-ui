(function(global, doc) {
    const CLASS_HIDDEN = 'ez-extra-actions--hidden';
    const CLASS_EXPANDED = 'ez-context-menu--expanded';
    const CLASS_ACTIVE_BUTTON = 'ez-btn--active-button';
    const CLASS_PREVENT_SHOW = 'ez-extra-actions--prevent-show';
    const ACTIONS_CONTAINER_MARGIN = 8;
    const RESIZE_AND_SCROLL_TIMEOUT = 50;
    const btns = [...doc.querySelectorAll('.ez-btn--extra-actions')];
    const menu = doc.querySelector('.ez-context-menu');
    const footer = doc.querySelector('.ez-footer');
    let containerHeightTimeout;
    const haveHiddenPart = (element) => element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW);
    const setContainerHeight = () => {
        const container = doc.querySelector('.ez-extra-actions:not(.ez-extra-actions--hidden)');
        const bottomPosition = Math.min(footer.getBoundingClientRect().top, global.innerHeight);
        const containerHeight = bottomPosition - container.getBoundingClientRect().top - ACTIONS_CONTAINER_MARGIN;

        container.style.height = `${containerHeight}px`;
    };
    const setContainerHeightTimeout = () => {
        clearTimeout(containerHeightTimeout);

        containerHeightTimeout = setTimeout(setContainerHeight, RESIZE_AND_SCROLL_TIMEOUT);
    };
    const addContainerHeightListeners = () => {
        global.addEventListener('scroll', setContainerHeightTimeout, false);
        global.addEventListener('resize', setContainerHeightTimeout, false);
    };
    const removeContainerHeightListeners = () => {
        global.removeEventListener('scroll', setContainerHeightTimeout, false);
        global.removeEventListener('resize', setContainerHeightTimeout, false);
    };

    btns.forEach((btn) => {
        btn.addEventListener(
            'click',
            () => {
                const actions = doc.querySelector(`.ez-extra-actions[data-actions="${btn.dataset.actions}"]`);

                const isHidden = haveHiddenPart(actions);
                const methodNameButton = isHidden ? 'add' : 'remove';
                const methodNameContainer = isHidden ? 'remove' : 'add';
                const methodNameMenu = isHidden ? 'add' : 'remove';
                const focusElement = actions.querySelector(btn.dataset.focusElement);
                const detectClickOutside = (event) => {
                    const isNotButton = !btn.contains(event.target);
                    const shouldCollapseMenu = !btns.includes(event.target);
                    const isNotExtraActions = !event.target.closest('.ez-extra-actions');
                    const isNotCalendar = !event.target.closest('.flatpickr-calendar');

                    if (isNotButton && isNotExtraActions && isNotCalendar) {
                        btn.classList.remove(CLASS_ACTIVE_BUTTON);
                        actions.classList.add(CLASS_HIDDEN);

                        if (shouldCollapseMenu) {
                            menu.classList.remove(CLASS_EXPANDED);
                        }

                        doc.body.removeEventListener('click', detectClickOutside, false);
                        removeContainerHeightListeners();
                    }
                };

                btn.classList[methodNameButton](CLASS_ACTIVE_BUTTON);
                actions.classList[methodNameContainer](CLASS_HIDDEN);
                menu.classList[methodNameMenu](CLASS_EXPANDED);

                if (!actions.classList.contains(CLASS_HIDDEN)) {
                    doc.body.addEventListener('click', detectClickOutside, false);
                    setContainerHeight();
                    addContainerHeightListeners();
                } else {
                    doc.body.removeEventListener('click', detectClickOutside);
                    removeContainerHeightListeners();
                }

                if (focusElement) {
                    focusElement.focus();
                }
            },
            false
        );
    });
})(window, window.document);
