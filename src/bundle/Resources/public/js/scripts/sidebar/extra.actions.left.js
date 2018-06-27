(function () {
    const CLASS_HIDDEN = 'ez-extra-actions-left--hidden';
    const CLASS_PREVENT_SHOW = 'ez-extra-actions-left--prevent-show';
    const btns = [...document.querySelectorAll('.ez-btn--extra-actions-left')];

    btns.forEach(btn => {
        btn.addEventListener('click', (event) => {
            const actions = document.querySelector(`.ez-extra-actions-left[data-actions="${btn.dataset.actions}"]`);
            const haveHiddenPart = (element) => {
                return element.classList.contains(CLASS_HIDDEN) && !element.classList.contains(CLASS_PREVENT_SHOW)
            };
            const methodName = haveHiddenPart(actions) ? 'remove' : 'add';
            const clickOutsideMethodName = actions.classList.contains(CLASS_HIDDEN) ? 'addEventListener' : 'removeEventListener';
            const detectClickLeftOutside = (event) => {
                const isNotButton = !event.target.contains(btn)
                    && !event.target.parentNode.contains(btn)
                    && event.target.parentNode !== btn
                    && event.target.parentNode.parentNode !== btn
                    && !event.target.classList.contains('jstree-icon');
                const isNotExtraActions = !event.target.closest('.ez-extra-actions-left');

                if (isNotButton && isNotExtraActions) {
                    actions.classList.add(CLASS_HIDDEN);
                    document.body.removeEventListener('click', detectClickLeftOutside, false);
                }
            };

            actions.style.top = (btn.offsetTop - 64) + 'px';
            actions.classList[methodName](CLASS_HIDDEN);
            document.body[clickOutsideMethodName]('click', detectClickLeftOutside, false);
        }, false);
    });
})();
