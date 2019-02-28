(function(doc, localStorage, $) {
    const CONTENT_PREVIEW_COLLAPSE_SELECTOR = '.ez-content-preview-collapse';
    const CONTENT_PREVIEW_TOGGLE_STATE_KEY = 'ez-content-preview-collapsed';
    const getContentPreviewToggleState = () => {
        const value = localStorage.getItem(CONTENT_PREVIEW_TOGGLE_STATE_KEY);
        if (value !== null) {
            return JSON.parse(value);
        }

        return false;
    };
    const setContentPreviewToggleState = (value) => {
        localStorage.setItem(CONTENT_PREVIEW_TOGGLE_STATE_KEY, value);
    };

    doc.querySelectorAll(CONTENT_PREVIEW_COLLAPSE_SELECTOR).forEach((collapsable) => {
        collapsable = $(collapsable).collapse({
            toggle: getContentPreviewToggleState(),
        });

        collapsable.on('hide.bs.collapse', () => setContentPreviewToggleState(true));
        collapsable.on('show.bs.collapse', () => setContentPreviewToggleState(false));
    });
})(document, window.localStorage, window.jQuery);
