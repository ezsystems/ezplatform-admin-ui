(function(global, doc, eZ) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const moreTab = doc.querySelector('.ez-tabs__tab--more');
    const moreTabLink = doc.querySelector('.ez-tabs__tab--more .nav-link');
    const tabsList = doc.querySelector('.ez-tabs');
    const allTabs = tabsList.querySelectorAll('.ez-tabs__tab');
    const secondaryTabsList = doc.querySelector('.ez-tabs--secondary');
    const primaryTabs = [...tabsList.querySelectorAll('.ez-tabs__tab--primary')];
    const secondaryTabs = [...secondaryTabsList.querySelectorAll('.ez-tabs__tab--secondary')];
    const adaptTabs = (toBeActiveNavLink = tabsList.querySelector('.ez-tabs__tab--primary .active')) => {
        primaryTabs.forEach((tab) => tab.classList.remove('ez-tabs__tab--hidden'));
        moreTab.classList.remove('ez-tabs__tab--hidden');

        const toBeActiveTab = toBeActiveNavLink ? toBeActiveNavLink.closest('.ez-tabs__tab') : null;
        const toBeActiveTabWidth = toBeActiveTab ? toBeActiveTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
        const moreTabWidth = moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;

        const maxTotalWidth = tabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
        let currentWidth = moreTabWidth + toBeActiveTabWidth;
        const hiddenPrimaryTabs = [];

        for (let i = 0; i < primaryTabs.length; ++i) {
            const tab = primaryTabs[i];
            const navLink = tab.querySelector('.nav-link');
            const isLastTab = i === primaryTabs.length - 1;
            const allPreviousTabsVisible = hiddenPrimaryTabs.length === 0;
            const isTabNarrowerThanMoreTab = tab.offsetWidth < moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;

            if (toBeActiveNavLink && toBeActiveNavLink.href === navLink.href) {
                continue;
            }

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

        const primaryActiveNavLink = tabsList.querySelector(`.ez-tabs__tab--primary a[href="${activeNavLink.getAttribute('href')}"]`);
        adaptTabs(primaryActiveNavLink);

        moreTab.classList.toggle('ez-tabs__tab--expanded', false);
        secondaryTabsList.classList.toggle('ez-tabs--hidden', true);
    };
    const highlightMoreTab = () => {
        const activeTabFromSecondaryMenu = secondaryTabsList.querySelector('.ez-tabs__tab--secondary:not(.ez-tabs__tab--hidden) .active'); // TODO: check selector

        moreTab.classList.toggle('ez-tabs__tab--highlighted', !!activeTabFromSecondaryMenu);
    };

    const tabIdMatch = global.location.hash.match(/^#(.*)#tab/i);
    const tabId = tabIdMatch ? tabIdMatch[1] : null;
    const activeNavLink = tabId ? tabsList.querySelector(`.ez-tabs__tab--primary a[href="#${tabId}"]`) : null;
    adaptTabs(activeNavLink);

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
