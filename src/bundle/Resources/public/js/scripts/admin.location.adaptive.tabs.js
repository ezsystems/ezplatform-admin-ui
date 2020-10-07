(function(global, doc, $, Translator) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const TABS_SELECTOR = '.ez-tabs';
    const copyTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabs) => {
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
        });
    };
    const adaptTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
            const primaryTabs = [...primaryTabsList.querySelectorAll(':scope > .ez-tabs__tab')];
            const primaryTabsLinks = [...primaryTabsList.querySelectorAll(':scope > .ez-tabs__tab .nav-link')];
            const moreTab = primaryTabsList.querySelector(':scope > .ez-tabs__tab--more');

            if (moreTab) {
                const secondaryTabs = [...moreTab.querySelectorAll('.ez-tabs__tab')];
                const activePrimaryTabLink = primaryTabsLinks.find((tabLink) => tabLink.classList.contains('active'));
                const activePrimaryTab = activePrimaryTabLink ? activePrimaryTabLink.closest('.ez-tabs__tab') : null;
                const activePrimaryTabWidth = activePrimaryTab ? activePrimaryTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
                const moreTabWidth = moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
                const maxTotalWidth = primaryTabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
                const hiddenPrimaryTabs = [];
                let currentWidth = moreTabWidth + activePrimaryTabWidth;

                primaryTabs.forEach((tab) => tab.classList.remove('ez-tabs__tab--hidden'));
                moreTab.classList.remove('ez-tabs__tab--hidden');

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

                primaryTabs.forEach((tab, index) => {
                    tab.classList.toggle('ez-tabs__tab--hidden', hiddenPrimaryTabs.includes(index));
                });
                secondaryTabs.forEach((tab, index) => {
                    tab.classList.toggle('ez-tabs__tab--hidden', !hiddenPrimaryTabs.includes(index));
                });

                moreTab.classList.toggle('ez-tabs__tab--hidden', !hiddenPrimaryTabs.length);
            }
        });
    };
    const handleClickOutsideSecondaryMenu = (event) => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
            const moreTab = primaryTabsList.querySelector('.ez-tabs__tab--more');

            if (moreTab) {
                const isSecondaryMenuExpanded = moreTab.classList.contains('ez-tabs__tab--expanded');
                const isClickInsideMoreTab = event.target.closest('.ez-tabs__tab--more');
                const secondaryTabsList = moreTab.querySelector('.ez-tabs--secondary');

                if (!isSecondaryMenuExpanded || isClickInsideMoreTab) {
                    return;
                }

                moreTab.classList.remove('ez-tabs__tab--expanded');
                secondaryTabsList.classList.add('ez-tabs--hidden');
            }
        });
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

    copyTabs();
    adaptTabs();

    doc.addEventListener('click', handleClickOutsideSecondaryMenu);

    doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
        const moreTab = primaryTabsList.querySelector('.ez-tabs__tab--more');

        if (moreTab) {
            const moreTabLink = primaryTabsList.querySelector('.ez-tabs__tab--more .nav-link');
            const secondaryTabsList = moreTab.querySelector('.ez-tabs--secondary');

            moreTabLink.addEventListener('click', () => {
                moreTab.classList.toggle('ez-tabs__tab--expanded');
                secondaryTabsList.classList.toggle('ez-tabs--hidden');
            });
        }
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
        const moreTab = primaryTabsList.querySelector('.ez-tabs__tab--more');
        const primaryTabsLinks = [...primaryTabsList.querySelectorAll('.ez-tabs__tab .nav-link')];

        if (moreTab) {
            const secondaryTabsList = moreTab.querySelector('.ez-tabs--secondary');
            const secondaryTabsLinks = [...secondaryTabsList.querySelectorAll('.ez-tabs__tab .nav-link')];

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
        }
    });

    document.body.addEventListener('ez-content-tree-resized', adaptTabs);
    window.addEventListener('resize', adaptTabs);
})(window, window.document, window.jQuery, window.Translator);
