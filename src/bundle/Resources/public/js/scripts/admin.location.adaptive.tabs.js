(function(global, doc, $, Translator) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const TABS_SELECTOR = '.ibexa-tabs';
    const copyTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabs) => {
            const moreLabel = Translator.trans(/*@Desc("More")*/ 'content.view.more.label', {}, 'content');

            primaryTabs.insertAdjacentHTML(
                'beforeend',
                `<li class="nav-item ibexa-tabs__tab ibexa-tabs__tab--more">
                <a class="nav-link" id="ez-tab-label--more" role="tab">${moreLabel}</a>
                <ul class="nav nav-tabs ibexa-tabs ibexa-tabs--hidden ibexa-tabs--secondary" role="tablist">
                    ${primaryTabs.innerHTML}
                </ul>
            </li>`
            );
        });
    };
    const adaptTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
            const primaryTabs = [...primaryTabsList.querySelectorAll(':scope > .ibexa-tabs__tab')];
            const primaryTabsLinks = [...primaryTabsList.querySelectorAll(':scope > .ibexa-tabs__tab .nav-link')];
            const moreTab = primaryTabsList.querySelector(':scope > .ibexa-tabs__tab--more');

            if (moreTab) {
                const secondaryTabs = [...moreTab.querySelectorAll('.ibexa-tabs__tab')];
                const activePrimaryTabLink = primaryTabsLinks.find((tabLink) => tabLink.classList.contains('active'));
                const activePrimaryTab = activePrimaryTabLink ? activePrimaryTabLink.closest('.ibexa-tabs__tab') : null;
                const activePrimaryTabWidth = activePrimaryTab ? activePrimaryTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
                const moreTabWidth = moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
                const maxTotalWidth = primaryTabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
                const hiddenPrimaryTabs = [];
                let currentWidth = moreTabWidth + activePrimaryTabWidth;

                primaryTabs.forEach((tab) => tab.classList.remove('ibexa-tabs__tab--hidden'));
                moreTab.classList.remove('ibexa-tabs__tab--hidden');

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
                    tab.classList.toggle('ibexa-tabs__tab--hidden', hiddenPrimaryTabs.includes(index));
                });
                secondaryTabs.forEach((tab, index) => {
                    tab.classList.toggle('ibexa-tabs__tab--hidden', !hiddenPrimaryTabs.includes(index));
                });

                moreTab.classList.toggle('ibexa-tabs__tab--hidden', !hiddenPrimaryTabs.length);
            }
        });
    };
    const handleClickOutsideSecondaryMenu = (event) => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
            const moreTab = primaryTabsList.querySelector('.ibexa-tabs__tab--more');

            if (moreTab) {
                const isSecondaryMenuExpanded = moreTab.classList.contains('ibexa-tabs__tab--expanded');
                const isClickInsideMoreTab = event.target.closest('.ibexa-tabs__tab--more');
                const secondaryTabsList = moreTab.querySelector('.ibexa-tabs--secondary');

                if (!isSecondaryMenuExpanded || isClickInsideMoreTab) {
                    return;
                }

                moreTab.classList.remove('ibexa-tabs__tab--expanded');
                secondaryTabsList.classList.add('ibexa-tabs--hidden');
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
        const moreTab = primaryTabsList.querySelector('.ibexa-tabs__tab--more');

        if (moreTab) {
            const moreTabLink = primaryTabsList.querySelector('.ibexa-tabs__tab--more .nav-link');
            const secondaryTabsList = moreTab.querySelector('.ibexa-tabs--secondary');

            moreTabLink.addEventListener('click', () => {
                moreTab.classList.toggle('ibexa-tabs__tab--expanded');
                secondaryTabsList.classList.toggle('ibexa-tabs--hidden');
            });
        }
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
        const moreTab = primaryTabsList.querySelector('.ibexa-tabs__tab--more');
        const primaryTabsLinks = [...primaryTabsList.querySelectorAll('.ibexa-tabs__tab .nav-link')];

        if (moreTab) {
            const secondaryTabsList = moreTab.querySelector('.ibexa-tabs--secondary');
            const secondaryTabsLinks = [...secondaryTabsList.querySelectorAll('.ibexa-tabs__tab .nav-link')];

            primaryTabsLinks.forEach((tabLink) => {
                $(tabLink).on('shown.bs.tab', (event) => {
                    handleTabClick(event, secondaryTabsLinks);
                });
            });

            secondaryTabsLinks.forEach((tabLink) => {
                tabLink.addEventListener('click', (event) => {
                    handleTabClick(event, primaryTabsLinks);

                    moreTab.classList.toggle('ibexa-tabs__tab--expanded', false);
                    secondaryTabsList.classList.toggle('ibexa-tabs--hidden', true);
                });
            });
        }
    });

    document.body.addEventListener('ez-content-tree-resized', adaptTabs);
    window.addEventListener('resize', adaptTabs);
})(window, window.document, window.jQuery, window.Translator);
