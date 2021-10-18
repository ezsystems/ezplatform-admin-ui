(function(global, doc, localStorage, bootstrap) {
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
        const bootstrapCollapsable = new bootstrap.Collapse(collapsable, {
            toggle: getContentPreviewToggleState(collapsable),
        });

        collapsable.addEventListener('hide.bs.collapse', (event) => setContentPreviewToggleState(event, true));
        collapsable.addEventListener('show.bs.collapse', (event) => setContentPreviewToggleState(event, false));
    });
})(window, window.document, window.localStorage, window.bootstrap);
