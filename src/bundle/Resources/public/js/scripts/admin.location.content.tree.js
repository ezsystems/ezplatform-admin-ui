(function(doc, React, ReactDOM, eZ, localStorage) {
    const KEY_CONTENT_TREE_EXPANDED = 'ez-content-tree-expanded';
    const CLASS_CONTENT_TREE_EXPANDED = 'ez-content-tree-container--expanded';
    const CLASS_BTN_CONTENT_TREE_EXPANDED = 'ez-btn--content-tree-expanded';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const contentTreeContainer = doc.querySelector('.ez-content-tree-container');
    const btn = doc.querySelector('.ez-btn--toggle-content-tree');
    const toggleContentTreePanel = () => {
        contentTreeContainer.classList.toggle(CLASS_CONTENT_TREE_EXPANDED);
        btn.classList.toggle(CLASS_BTN_CONTENT_TREE_EXPANDED);

        localStorage.setItem(KEY_CONTENT_TREE_EXPANDED, contentTreeContainer.classList.contains(CLASS_CONTENT_TREE_EXPANDED));
    };

    ReactDOM.render(
        React.createElement(eZ.modules.ContentTree, {
            currentLocationId: parseInt(contentTreeContainer.dataset.currentLocationId, 10),
            restInfo: { token, siteaccess },
        }),
        contentTreeContainer
    );

    btn.addEventListener('click', toggleContentTreePanel, false);

    if (localStorage.getItem(KEY_CONTENT_TREE_EXPANDED) === 'true') {
        contentTreeContainer.classList.add(CLASS_CONTENT_TREE_EXPANDED);
        btn.classList.add(CLASS_BTN_CONTENT_TREE_EXPANDED);
    }
})(window.document, window.React, window.ReactDOM, window.eZ, window.localStorage);
