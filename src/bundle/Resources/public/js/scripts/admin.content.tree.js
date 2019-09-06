(function(global, doc, React, ReactDOM, eZ, localStorage) {
    // const initializeAdaptiveTabs = () => {
    //     const primaryTabsList = doc.querySelector('.ez-tabs');
    //     const primaryTabs = [...primaryTabsList.querySelectorAll('.ez-tabs__tab--primary')];
    //     // const moreTab = primaryTabsList.querySelector('.ez-tabs__tab--more');
    //     const secondaryTabsList = doc.querySelector('.ez-tabs__secondary');

    //     primaryTabs.forEach((tab) => {
    //         secondaryTabsList.insertAdjacentHTML('beforeend', tab.outerHTML);
    //     });
    // };
    const adaptTabs = () => {
        const tabsList = doc.querySelector('.ez-tabs');
        const primaryTabs = [...tabsList.querySelectorAll('.ez-tabs__tab--primary:not(.ez-tabs__tab--more)')];
        const secondaryTabsList = doc.querySelector('.ez-tabs--secondary');
        const secondaryTabs = [...secondaryTabsList.querySelectorAll('.ez-tabs__tab--secondary')];
        const moreTab = doc.querySelector('.ez-tabs__tab--more');

        primaryTabs.forEach((tab) => tab.classList.remove('ez-tabs__tab--hidden'));
        moreTab.classList.remove('ez-tabs__tab--hidden');

        const maxTotalWidth = tabsList.offsetWidth + 1; // TODO: change 1 to 0.5
        let currentWidth = moreTab.offsetWidth + 1; // rethink assigning moreTab.affWi...
        const hiddenPrimaryTabs = [];

        primaryTabs.forEach((tab, index) => {
            if (currentWidth + tab.offsetWidth + 1 > maxTotalWidth) {
                hiddenPrimaryTabs.push(index);
            }

            currentWidth += tab.offsetWidth + 1;
        });

        moreTab.classList.toggle('ez-tabs__tab--hidden', !hiddenPrimaryTabs.length);
        primaryTabs.forEach((tab, index) => {
            tab.classList.toggle('ez-tabs__tab--hidden', hiddenPrimaryTabs.includes(index));
        });
        secondaryTabs.forEach((tab, index) => {
            tab.classList.toggle('ez-tabs__tab--hidden', !hiddenPrimaryTabs.includes(index));
        });
    };

    const KEY_CONTENT_TREE_EXPANDED = 'ez-content-tree-expanded';
    const CLASS_CONTENT_TREE_EXPANDED = 'ez-content-tree-container--expanded';
    const CLASS_BTN_CONTENT_TREE_EXPANDED = 'ez-btn--content-tree-expanded';
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
    const contentTreeContainer = doc.querySelector('.ez-content-tree-container');
    const contentTreeWrapper = doc.querySelector('.ez-content-tree-container__wrapper');
    const btn = doc.querySelector('.ez-btn--toggle-content-tree');
    const { currentLocationPath, userId, treeRootLocationId } = contentTreeContainer.dataset;
    let frame = null;
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
            rootLocationId: treeRootLocationId,
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

    doc.addEventListener('scroll', handleViewportChange, { capture: false, passive: true });
    window.addEventListener('resize', handleViewportChange, { capture: false, passive: true });

    adaptTabs();
    // initializeAdaptiveTabs();
    const moreTab = doc.querySelector('.ez-tabs__tab--more');
    const moreTabLink = doc.querySelector('.ez-tabs__tab--more .nav-link');
    const tabsList = doc.querySelector('.ez-tabs');
    const allTabs = tabsList.querySelectorAll('.ez-tabs__tab');
    const secondaryTabsList = doc.querySelector('.ez-tabs--secondary');
    const handleClickOutsideSecondaryMenu = (event) => {
        const isClickInsideMoreTab = event.target.closest('.ez-tabs__tab--more');
        const isSecondaryMenuExpanded = moreTab.classList.contains('ez-tabs__tab--expanded');

        if (isClickInsideMoreTab || !isSecondaryMenuExpanded) {
            return;
        }

        // const isClickInsideMenu = event.target.closest('.ez-tabs--secondary');

        // if (!isClickInsideMenu) {
        moreTab.classList.remove('ez-tabs__tab--expanded');
        secondaryTabsList.classList.add('ez-tabs--hidden');
        // doc.removeEventListener('click', handleClickOutsideSecondaryMenu);
        // }
    };
    const handleTabClick = (event) => {
        const activeNavLink = event.currentTarget.querySelector('.nav-link');
        const navLinks = tabsList.querySelectorAll('.nav-link');

        if (!activeNavLink.href) {
            return;
        }

        navLinks.forEach((navLink) => {
            if (navLink === activeNavLink) {
                return;
            }

            navLink.classList.toggle('active', navLink.href === activeNavLink.href);
        });
    };
    doc.addEventListener('click', handleClickOutsideSecondaryMenu);
    moreTabLink.addEventListener('click', () => {
        // const isSecondaryMenuExpanded = moreTab.classList.contains('ez-tabs__tab--expanded');
        moreTab.classList.toggle('ez-tabs__tab--expanded');
        secondaryTabsList.classList.toggle('ez-tabs--hidden');
    });
    document.body.addEventListener('ez-content-tree-resized', () => {
        adaptTabs();
    });
    window.addEventListener('resize', () => {
        adaptTabs();
    });
    allTabs.forEach((tab) => {
        tab.addEventListener('click', handleTabClick);
    });
    
})(window, window.document, window.React, window.ReactDOM, window.eZ, window.localStorage);
