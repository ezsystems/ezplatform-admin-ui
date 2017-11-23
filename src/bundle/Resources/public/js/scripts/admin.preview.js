(function (global, doc) {
    const CLASS_BTN_SELECTED = 'ez-preview__action--selected';
    const SELECTOR_BTN_ACTION = '.ez-preview__action';
    const removeSelectedState = () => [...doc.querySelectorAll(SELECTOR_BTN_ACTION)].forEach(btn => btn.classList.remove(CLASS_BTN_SELECTED));
    const changePreviewMode = event => {
        const btn = event.target.closest(SELECTOR_BTN_ACTION);
        const iframeWrapper = doc.querySelector('.ez-preview__iframe');

        removeSelectedState();

        btn.classList.add(CLASS_BTN_SELECTED);

        iframeWrapper.classList.remove('ez-preview__iframe--desktop', 'ez-preview__iframe--tablet', 'ez-preview__iframe--mobile');
        iframeWrapper.classList.add(`ez-preview__iframe--${btn.dataset.previewMode}`)
    };

    [...doc.querySelectorAll(SELECTOR_BTN_ACTION)].forEach(btn => btn.addEventListener('click', changePreviewMode, false));
})(window, document);
