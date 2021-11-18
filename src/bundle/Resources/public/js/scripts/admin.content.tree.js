(function (global, doc, React, ReactDOM, eZ) {
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const contentTreeContainer = doc.querySelector('.ibexa-content-tree-container');
    const contentTreeWrapper = doc.querySelector('.ibexa-content-tree-container__wrapper');
    const { currentLocationPath, treeRootLocationId } = contentTreeContainer.dataset;
    const userId = window.eZ.helpers.user.getId();
    const removeContentTreeContainerWidth = () => {
        contentTreeContainer.style.width = null;
    }
    const renderTree = () => {
        ReactDOM.render(
            React.createElement(eZ.modules.ContentTree, {
                userId,
                currentLocationPath,
                rootLocationId: parseInt(treeRootLocationId, 10),
                restInfo: { token, siteaccess },
            }),
            contentTreeWrapper
        );
    }

    doc.body.addEventListener('ibexa-tb-rendered:ibexa-content-tree', removeContentTreeContainerWidth);

    renderTree();
})(window, window.document, window.React, window.ReactDOM, window.eZ);
