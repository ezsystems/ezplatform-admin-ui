(function (global, doc, React, ReactDOM, eZ, localStorage) {
    const KEY_CONTENT_TREE_EXPANDED = 'ez-content-tree-expanded';
    const CLASS_CONTENT_TREE_EXPANDED = 'ez-content-tree-container--expanded';
    const CLASS_CONTENT_TREE_ANIMATE = 'ez-content-tree-container--animate';
    const CLASS_BTN_CONTENT_TREE_EXPANDED = 'ibexa-btn--content-tree-expanded';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const contentTreeContainer = doc.querySelector('.ez-content-tree-container');
    const contentTreeWrapper = doc.querySelector('.ez-content-tree-container__wrapper');
    const btn = doc.querySelector('.ibexa-btn--toggle-content-tree');
    const { currentLocationPath, treeRootLocationId } = contentTreeContainer.dataset;
    const userId = window.eZ.helpers.user.getId();
    let frame = null;
    const toggleContentTreePanel = () => {
        doc.activeElement.blur();
        contentTreeContainer.classList.toggle(CLASS_CONTENT_TREE_EXPANDED);
        contentTreeContainer.classList.add(CLASS_CONTENT_TREE_ANIMATE);
        btn.classList.toggle(CLASS_BTN_CONTENT_TREE_EXPANDED);
        updateContentTreeWrapperHeight();

        const isContentTreeExpanded = contentTreeContainer.classList.contains(CLASS_CONTENT_TREE_EXPANDED);

        saveContentTreeExpandedState(userId, isContentTreeExpanded);
        eZ.helpers.tooltips.hideAll();
    };
    const updateContentTreeWrapperHeight = () => {
        const height = Math.min(window.innerHeight - contentTreeContainer.getBoundingClientRect().top, window.innerHeight);

        contentTreeWrapper.style.height = `${height}px`;
    };
    const handleViewportChange = () => {
        if (frame) {
            cancelAnimationFrame(frame);
        }

        frame = requestAnimationFrame(updateContentTreeWrapperHeight);
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
            rootLocationId: parseInt(treeRootLocationId, 10),
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

    let transitionCount = 0;
    let transitionEventIntervalId = null;
    const dispatchContentTreeResizeEvent = () => doc.body.dispatchEvent(new CustomEvent('ez-content-tree-resized'));
    const handleContainerTransitionStart = () => {
        if (transitionCount == 0) {
            transitionEventIntervalId = setInterval(dispatchContentTreeResizeEvent, 30);
        }

        transitionCount += 1;
    };
    const handleContainerTransitionStop = () => {
        transitionCount -= 1;

        if (transitionCount == 0) {
            clearInterval(transitionEventIntervalId);
            dispatchContentTreeResizeEvent();
        }
    };

    contentTreeContainer.addEventListener('transitionstart', handleContainerTransitionStart, false);
    contentTreeContainer.addEventListener('transitioncancel', handleContainerTransitionStop, false);
    contentTreeContainer.addEventListener('transitionend', handleContainerTransitionStop, false);

    window.addEventListener('resize', handleViewportChange, { capture: false, passive: true });
})(window, window.document, window.React, window.ReactDOM, window.eZ, window.localStorage);
