(function(global, doc) {
    const CLASS_HIDDEN = 'ibexa-extra-actions--hidden';
    const CLASS_EXPANDED = 'ibexa-context-menu--expanded';
    const CLASS_PREVENT_SHOW = 'ibexa-extra-actions--prevent-show';
    const btns = [...doc.querySelectorAll('.ibexa-btn--extra-actions')];
    const menu = doc.querySelector('.ibexa-context-menu');
    let containerHeightTimeout;
    const haveHiddenPart = (element) => element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW);
    const removeBackdrop = () => {
        const backdrop = doc.querySelector('.ibexa-backdrop');

        if (backdrop) {
            backdrop.remove();
            doc.body.classList.remove('ez-scroll-disabled');
        }
    };
    const closeExtraActions = (actions) => {
        actions.classList.add(CLASS_HIDDEN);

        if (menu) {
            menu.classList.remove(CLASS_EXPANDED);
        }

        removeBackdrop();
    };

    btns.forEach((btn) => {
        btn.addEventListener(
            'click',
            () => {
                const actions = doc.querySelector(`.ibexa-extra-actions[data-actions="${btn.dataset.actions}"]`);if (btn.dataset.validate && !parseInt(btn.dataset.isFormValid, 10)) {
                    return;
                }
                const isHidden = haveHiddenPart(actions);
                const methodNameButton = isHidden ? 'add' : 'remove';
                const methodNameContainer = isHidden ? 'remove' : 'add';
                const methodNameMenu = isHidden ? 'add' : 'remove';
                const focusElement = actions.querySelector(btn.dataset.focusElement);
                const relatedNodeTrigger = doc.querySelector(`[data-related-button-id="${btn.id}"]`);
                const detectClickOutside = (event) => {
                    if (event.target.classList.contains('ibexa-backdrop')) {
                        closeExtraActions(actions);
                        doc.body.removeEventListener('click', detectClickOutside, false);
                    }
                };

                actions.classList[methodNameContainer](CLASS_HIDDEN);

                if (menu) {
                    menu.classList[methodNameMenu](CLASS_EXPANDED);
                }

                if (!actions.classList.contains(CLASS_HIDDEN)) {
                    const backdrop = doc.createElement('div');

                    backdrop.classList.add('ibexa-backdrop');

                    doc.body.addEventListener('click', detectClickOutside, false);
                    doc.body.appendChild(backdrop);
                    doc.body.classList.add('ez-scroll-disabled');
                } else {
                    doc.body.removeEventListener('click', detectClickOutside);
                    removeBackdrop();
                }

                if (focusElement) {
                    focusElement.focus();
                }
            },
            false
        );
    });

    doc.querySelectorAll('.ibexa-extra-actions .ibexa-btn--close').forEach((closeBtn) =>
        closeBtn.addEventListener(
            'click',
            (event) => {
                closeExtraActions(event.currentTarget.closest('.ibexa-extra-actions'));
            },
            false
        )
    );
})(window, window.document);
