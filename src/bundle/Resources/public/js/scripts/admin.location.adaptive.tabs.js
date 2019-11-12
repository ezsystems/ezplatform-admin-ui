(function(global, doc, $, Translator) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const copyTabs = () => {
        const primaryTabs = doc.querySelector('.ez-tabs');
        const moreLabel = Translator.trans(/*@Desc("More")*/ 'content.view.more.label', {}, 'content');

        primaryTabs.insertAdjacentHTML(
            'beforeend',
            `<li class="nav-item ez-tabs__tab ez-tabs__tab--more">
                <a class="nav-link" id="ez-tab-label--more" role="tab">${moreLabel}</a>
                <ul class="nav nav-tabs ez-tabs ez-tabs--hidden ez-tabs--secondary" role="tablist">
                    ${primaryTabs.innerHTML}
                </ul>
            </li>`
        );
    };
    const primaryTabsList = doc.querySelector('.ez-tabs');
    const primaryTabs = [...primaryTabsList.querySelectorAll('.ez-tabs__tab')];
    const primaryTabsLinks = [...primaryTabsList.querySelectorAll('.ez-tabs__tab .nav-link')];

    copyTabs();

    const moreTab = primaryTabsList.querySelector('.ez-tabs__tab--more');
    const moreTabLink = primaryTabsList.querySelector('.ez-tabs__tab--more .nav-link');
    const secondaryTabsList = moreTab.querySelector('.ez-tabs--secondary');
    const secondaryTabs = [...moreTab.querySelectorAll('.ez-tabs__tab')];
    const secondaryTabsLinks = [...secondaryTabsList.querySelectorAll('.ez-tabs__tab .nav-link')];
    const adaptTabs = () => {
        primaryTabs.forEach((tab) => tab.classList.remove('ez-tabs__tab--hidden'));
        moreTab.classList.remove('ez-tabs__tab--hidden');

        const activePrimaryTabLink = primaryTabsLinks.find((tabLink) => tabLink.classList.contains('active'));
        const activePrimaryTab = activePrimaryTabLink ? activePrimaryTabLink.closest('.ez-tabs__tab') : null;

        const activePrimaryTabWidth = activePrimaryTab ? activePrimaryTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
        const moreTabWidth = moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;

        const maxTotalWidth = primaryTabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
        let currentWidth = moreTabWidth + activePrimaryTabWidth;
        const hiddenPrimaryTabs = [];

        for (let i = 0; i < primaryTabs.length; i++) {
            const tab = primaryTabs[i];
            const tabLink = tab.querySelector('.nav-link');

            if (tabLink === activePrimaryTabLink) {
                continue;
            }

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
    const handleTabClick = (event, otherTabsLinks) => {
        const clickedTabLink = event.currentTarget;
        const otherTabLinkToShow = otherTabsLinks.find((otherTabLink) => otherTabLink.href === clickedTabLink.href);

        if (!otherTabLinkToShow) {
            return;
        }

        $(otherTabLinkToShow).tab('show');
        adaptTabs();
    };

    adaptTabs();

    doc.addEventListener('click', handleClickOutsideSecondaryMenu);
    moreTabLink.addEventListener('click', () => {
        moreTab.classList.toggle('ez-tabs__tab--expanded');
        secondaryTabsList.classList.toggle('ez-tabs--hidden');
    });
    primaryTabsLinks.forEach((tabLink) => {
        $(tabLink).on('shown.bs.tab', (event) => {
            handleTabClick(event, secondaryTabsLinks);
        });
    });
    secondaryTabsLinks.forEach((tabLink) => {
        tabLink.addEventListener('click', (event) => {
            handleTabClick(event, primaryTabsLinks);

            moreTab.classList.toggle('ez-tabs__tab--expanded', false);
            secondaryTabsList.classList.toggle('ez-tabs--hidden', true);
        });
    });
    document.body.addEventListener('ez-content-tree-resized', adaptTabs);
    window.addEventListener('resize', adaptTabs);
})(window, window.document, window.jQuery, window.Translator);
