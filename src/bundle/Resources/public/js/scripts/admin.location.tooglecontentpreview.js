(function (global, doc, localStorage, $) {
    const CONTENT_PREVIEW_COLLAPSE_SELECTOR = '.ez-content-preview-collapse';
    const DEFAULT_CONTENT_PREVIEW_TOGGLE_STATE_KEY = 'ez-content-preview-collapsed';
    const getStateKey = (collapseTarget) => {
        return collapseTarget.dataset.collapseStateKey || DEFAULT_CONTENT_PREVIEW_TOGGLE_STATE_KEY;
    };
    const getContentPreviewToggleState = (collapsable) => {
        const stateKey = getStateKey(collapsable);
        const value = localStorage.getItem(stateKey);

        return !!JSON.parse(value);
    };
    const setContentPreviewToggleState = (event, value) => {
        const stateKey = getStateKey(event.target);
        localStorage.setItem(stateKey, value);
    };

    doc.querySelectorAll(CONTENT_PREVIEW_COLLAPSE_SELECTOR).forEach((collapsable) => {
        collapsable = $(collapsable).collapse({
            toggle: getContentPreviewToggleState(collapsable),
        });

        collapsable.on('hide.bs.collapse', (event) => setContentPreviewToggleState(event, true));
        collapsable.on('show.bs.collapse', (event) => setContentPreviewToggleState(event, false));
    });
})(window, window.document, window.localStorage, window.jQuery);
