(function(doc, React, ReactDOM, eZ, localStorage) {
    const KEY_CONTENT_TREE_EXPANDED = 'ez-content-tree-expanded';
    const CLASS_CONTENT_TREE_EXPANDED = 'ez-content-tree-container--expanded';
    const CLASS_BTN_CONTENT_TREE_EXPANDED = 'ez-btn--content-tree-expanded';
    const VIEWPORT_CHANGE_TIMEOUT = 50;
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const contentTreeContainer = doc.querySelector('.ez-content-tree-container');
    const contentTreeWrapper = doc.querySelector('.ez-content-tree-container__wrapper');
    const btn = doc.querySelector('.ez-btn--toggle-content-tree');
    const { currentLocationPath, userId } = contentTreeContainer.dataset;
    let onViewportChangeTimeout = null;
    const toggleContentTreePanel = () => {
        contentTreeContainer.classList.toggle(CLASS_CONTENT_TREE_EXPANDED);
        btn.classList.toggle(CLASS_BTN_CONTENT_TREE_EXPANDED);
        updateContentTreeWrapperHeight();

        const isContentTreeExpanded = contentTreeContainer.classList.contains(CLASS_CONTENT_TREE_EXPANDED);

        saveContentTreeExpandedState(userId, isContentTreeExpanded);
    };
    const updateContentTreeWrapperHeight = () => {
        const height = Math.min(window.innerHeight - contentTreeContainer.getBoundingClientRect().top, window.innerHeight);

        contentTreeWrapper.style.height = `${height}px`;
        onViewportChangeTimeout = null;
    };
    const handleViewportChange = () => {
        if (onViewportChangeTimeout) {
            return;
        }

        window.clearTimeout(onViewportChangeTimeout);
        onViewportChangeTimeout = window.setTimeout(updateContentTreeWrapperHeight, VIEWPORT_CHANGE_TIMEOUT);
    };
    const saveContentTreeExpandedState = (userId, isExpanded) => {
        let expandedState = JSON.parse(localStorage.getItem(KEY_CONTENT_TREE_EXPANDED));

        if (!(expandedState instanceof Object)) {
            expandedState = {};
        }

        expandedState[userId] = isExpanded;

        localStorage.setItem(KEY_CONTENT_TREE_EXPANDED, JSON.stringify(expandedState));
    };
    const isContentTreeExpanded = (userId) => {
        const expandedState = JSON.parse(localStorage.getItem(KEY_CONTENT_TREE_EXPANDED));

        return expandedState && expandedState[userId];
    };

    ReactDOM.render(
        React.createElement(eZ.modules.ContentTree, {
            userId,
            currentLocationPath,
            restInfo: { token, siteaccess },
        }),
        contentTreeWrapper
    );

    btn.addEventListener('click', toggleContentTreePanel, false);

    if (isContentTreeExpanded(userId)) {
        contentTreeContainer.classList.add(CLASS_CONTENT_TREE_EXPANDED);
        btn.classList.add(CLASS_BTN_CONTENT_TREE_EXPANDED);
    }

    updateContentTreeWrapperHeight();

    doc.addEventListener('scroll', handleViewportChange, false);
    window.addEventListener('resize', handleViewportChange, false);
})(window.document, window.React, window.ReactDOM, window.eZ, window.localStorage);
