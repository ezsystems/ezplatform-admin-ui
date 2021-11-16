(function(global, doc) {
    const CLASS_BTN_SELECTED = 'ibexa-preview-header__action--selected';
    const SELECTOR_BTN_ACTION = '.ibexa-preview-header__action';
    const SELECTOR_PREVIEW_SITEACCESS_SELECT = '.ibexa-preview-header__item--siteaccess select';
    const removeSelectedState = () => doc.querySelectorAll(SELECTOR_BTN_ACTION).forEach((btn) => btn.classList.remove(CLASS_BTN_SELECTED));
    const changePreviewMode = (event) => {
        const btn = event.target.closest(SELECTOR_BTN_ACTION);
        const iframeWrapper = doc.querySelector('.ibexa-preview__iframe');

        removeSelectedState();

        btn.classList.add(CLASS_BTN_SELECTED);

        iframeWrapper.classList.remove('ibexa-preview__iframe--desktop', 'ibexa-preview__iframe--tablet', 'ibexa-preview__iframe--mobile');
        iframeWrapper.classList.add(`ibexa-preview__iframe--${btn.dataset.previewMode}`);
    };
    const changePreviewSiteaccess = (event) => {
        const iframeWrapper = doc.querySelector('.ibexa-preview__iframe iframe');
        const siteaccessPreviewUrl = event.target.value;

        iframeWrapper.setAttribute('src', siteaccessPreviewUrl);
    };
    doc.querySelectorAll(SELECTOR_BTN_ACTION).forEach((btn) => btn.addEventListener('click', changePreviewMode, false));
    doc.querySelectorAll(SELECTOR_PREVIEW_SITEACCESS_SELECT).forEach((select) =>
        select.addEventListener('change', changePreviewSiteaccess, false)
    );
})(window, window.document);
