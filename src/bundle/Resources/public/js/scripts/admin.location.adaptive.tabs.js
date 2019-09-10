(function(global, doc, eZ) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const moreTab = doc.querySelector('.ez-tabs__tab--more');
    const moreTabLink = doc.querySelector('.ez-tabs__tab--more .nav-link');
    const tabsList = doc.querySelector('.ez-tabs');
    const allTabs = tabsList.querySelectorAll('.ez-tabs__tab');
    const secondaryTabsList = doc.querySelector('.ez-tabs--secondary');
    const primaryTabs = [...tabsList.querySelectorAll('.ez-tabs__tab--primary')];
    const secondaryTabs = [...secondaryTabsList.querySelectorAll('.ez-tabs__tab--secondary')];
    const adaptTabs = () => {
        primaryTabs.forEach((tab) => tab.classList.remove('ez-tabs__tab--hidden'));
        moreTab.classList.remove('ez-tabs__tab--hidden');

        const maxTotalWidth = tabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
        let currentWidth = moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
        const hiddenPrimaryTabs = [];

        for (let i = 0; i < primaryTabs.length; ++i) {
            const tab = primaryTabs[i];
            const isLastTab = i === primaryTabs.length - 1;
            const allPreviousTabsVisible = hiddenPrimaryTabs.length === 0;
            const isTabNarrowerThanMoreTab = tab.offsetWidth < moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;

            if (isLastTab && allPreviousTabsVisible && isTabNarrowerThanMoreTab) {
                break;
            }

            if (currentWidth + tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR > maxTotalWidth) {
                hiddenPrimaryTabs.push(i);
            }

            currentWidth += tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
        }

        moreTab.classList.toggle('ez-tabs__tab--hidden', !hiddenPrimaryTabs.length);
        primaryTabs.forEach((tab, index) => {
            tab.classList.toggle('ez-tabs__tab--hidden', hiddenPrimaryTabs.includes(index));
        });
        secondaryTabs.forEach((tab, index) => {
            tab.classList.toggle('ez-tabs__tab--hidden', !hiddenPrimaryTabs.includes(index));
        });
    };
    const handleClickOutsideSecondaryMenu = (event) => {
        const isSecondaryMenuExpanded = moreTab.classList.contains('ez-tabs__tab--expanded');
        const isClickInsideMoreTab = event.target.closest('.ez-tabs__tab--more');

        if (!isSecondaryMenuExpanded || isClickInsideMoreTab) {
            return;
        }

        moreTab.classList.remove('ez-tabs__tab--expanded');
        secondaryTabsList.classList.add('ez-tabs--hidden');
    };
    const handleTabClick = (event) => {
        const activeNavLink = event.currentTarget.querySelector('.nav-link');
        const navLinks = tabsList.querySelectorAll('.nav-link');
        const isMoreTab = !activeNavLink.href; // TODO: maybe check CSS class ???

        if (isMoreTab) {
            return;
        }

        navLinks.forEach((navLink) => {
            if (navLink === activeNavLink) {
                return;
            }

            navLink.classList.toggle('active', navLink.href === activeNavLink.href);
        });

        const isInSecondaryMenu = !!event.target.closest('.ez-tabs--secondary');

        moreTab.classList.toggle('ez-tabs__tab--highlighted', isInSecondaryMenu);

        moreTab.classList.toggle('ez-tabs__tab--expanded', false);
        secondaryTabsList.classList.toggle('ez-tabs--hidden', true);
    };
    const highlightMoreTab = () => {
        const activeTabFromSecondaryMenu = secondaryTabsList.querySelector('.ez-tabs__tab--secondary:not(.ez-tabs__tab--hidden) .active'); // TODO: check selector

        moreTab.classList.toggle('ez-tabs__tab--highlighted', !!activeTabFromSecondaryMenu);
    };

    adaptTabs();

    doc.addEventListener('click', handleClickOutsideSecondaryMenu);
    moreTabLink.addEventListener('click', () => {
        moreTab.classList.toggle('ez-tabs__tab--expanded');
        secondaryTabsList.classList.toggle('ez-tabs--hidden');
        highlightMoreTab();
    });
    document.body.addEventListener('ez-content-tree-resized', () => {
        adaptTabs();
        highlightMoreTab();
    });
    window.addEventListener('resize', () => {
        adaptTabs();
        highlightMoreTab();
    });
    allTabs.forEach((tab) => {
        tab.addEventListener('click', handleTabClick);
        highlightMoreTab();
    });
})(window, window.document, window.eZ);
