(function (global, doc, $, Translator) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const TABS_SELECTOR = '.ibexa-tabs';
    const SELECTOR_TABS_LIST = '.ibexa-tabs__list';
    const SELECTOR_TAB_MORE = '.ibexa-tabs__tab--more';
    const copyTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabs) => {
            const tabsList = primaryTabs.querySelector(SELECTOR_TABS_LIST);
            const tabs = tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more)');
            const tabMore = tabsList.querySelector(':scope > .ibexa-tabs__tab--more');
            const contextMenu = tabMore.querySelector('.ibexa-context-menu');
            const tabTemplate = tabMore.dataset.itemTemplate;
            const fragment = doc.createDocumentFragment();

            tabs.forEach((tab) => {
                const tabLink = tab.querySelector('.ibexa-tabs__link');
                const tabLinkLabel = tabLink.textContent;
                const container = doc.createElement('ul');
                const renderedItem = tabTemplate.replace('{{ label }}', tabLinkLabel);

                container.insertAdjacentHTML('beforeend', renderedItem);

                const contextMenuItem = container.querySelector('li');

                contextMenuItem.dataset.tabLinkId = tabLink.id;
                fragment.append(contextMenuItem);
            });

            contextMenu.innerHTML = '';
            contextMenu.append(fragment);
        });
    };
    const adaptTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
            const tabsList = primaryTabsList.querySelector(SELECTOR_TABS_LIST);
            const primaryTabs = [...tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more)')];
            const primaryTabsLinks = [...tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more) .nav-link')];
            const moreTab = tabsList.querySelector(':scope > .ibexa-tabs__tab--more');

            if (moreTab) {
                [moreTab, ...primaryTabs].forEach((tab) => tab.classList.remove('ibexa-tabs__tab--hidden'));

                const contextMenuItems = [...moreTab.querySelectorAll('.ibexa-context-menu__item')];
                const activePrimaryTabLink = primaryTabsLinks.find((tabLink) => tabLink.classList.contains('active'));
                const activePrimaryTab = activePrimaryTabLink ? activePrimaryTabLink.closest('.ibexa-tabs__tab') : null;
                const activePrimaryTabWidth = activePrimaryTab ? activePrimaryTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
                const moreTabWidth = moreTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
                const maxTotalWidth = tabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
                const hiddenPrimaryTabsIds = [];
                let currentWidth = moreTabWidth + activePrimaryTabWidth;

                for (let i = 0; i < primaryTabs.length; i++) {
                    const tab = primaryTabs[i];
                    const tabLink = tab.querySelector('.nav-link');

                    if (tabLink === activePrimaryTabLink) {
                        continue;
                    }

                    const isLastTab = i === primaryTabs.length - 1;
                    const allPreviousTabsVisible = hiddenPrimaryTabsIds.length === 0;
                    const fitsInsteadOfMoreTab =
                        tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR < maxTotalWidth - currentWidth + moreTabWidth;

                    if (isLastTab && allPreviousTabsVisible && fitsInsteadOfMoreTab) {
                        break;
                    }

                    if (tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR > maxTotalWidth - currentWidth) {
                        hiddenPrimaryTabsIds.push(tab.dataset.linkId);
                    }

                    currentWidth += tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
                }

                primaryTabs.forEach((tab) => {
                    tab.classList.toggle('ibexa-tabs__tab--hidden', hiddenPrimaryTabsIds.includes(tab.dataset.linkId));
                });
                contextMenuItems.forEach((item) => {
                    item.classList.toggle('ibexa-context-menu__item--hidden', !hiddenPrimaryTabsIds.includes(item.dataset.tabLinkId));
                });

                moreTab.classList.toggle('ibexa-tabs__tab--hidden', !hiddenPrimaryTabsIds.length);
            }
        });
    };
    const handleClickOutsideSecondaryMenu = (event) => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
            const tabsList = primaryTabsList.querySelector(SELECTOR_TABS_LIST);
            const moreTab = tabsList.querySelector('.ibexa-tabs__tab--more');

            if (moreTab) {
                const popupMenu = moreTab.querySelector('.ibexa-tabs__context-menu');
                const isPopupMenuExpanded = !popupMenu.classList.contains('ibexa-tabs__context-menu--hidden');
                const isClickInsideMoreTab = event.target.closest('.ibexa-tabs__tab--more');
                // const secondaryTabsList = moreTab.querySelector('.ibexa-tabs--secondary');

                if (!isPopupMenuExpanded || isClickInsideMoreTab) {
                    return;
                }

                popupMenu.classList.add('ibexa-tabs__context-menu--hidden');
                // secondaryTabsList.classList.add('ibexa-tabs--hidden');
            }
        });
    };
    const toggleContainer = (event) => {
        const toggler = event.target;
        const header = toggler.closest('.ez-header');
        const headerContainer = header.parentElement;
        const tabContent = headerContainer.querySelector('.ibexa-tab-content');
        const isTabContentRolledUp = toggler.classList.contains('ibexa-tabs__toggler--rolled-up');

        toggler.classList.toggle('ibexa-tabs__toggler--rolled-up');
        tabContent.style.height = isTabContentRolledUp ? 'auto' : '0px';
    };

    doc.querySelectorAll('.ibexa-tabs__toggler').forEach((toggler) => {
        toggler.addEventListener('click', toggleContainer);
    });
    // const handleTabClick = (event, otherTabsLinks) => {
    //     const clickedTabLink = event.currentTarget;
    //     const otherTabLinkToShow = otherTabsLinks.find((otherTabLink) => otherTabLink.href === clickedTabLink.href);

    //     if (!otherTabLinkToShow) {
    //         return;
    //     }

    //     $(otherTabLinkToShow).tab('show');
    //     adaptTabs();
    // };

    copyTabs();
    adaptTabs();

    doc.addEventListener('click', handleClickOutsideSecondaryMenu);

    doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
        const tabsList = primaryTabsList.querySelector(SELECTOR_TABS_LIST);
        const moreTab = tabsList.querySelector('.ibexa-tabs__tab--more');

        if (moreTab) {
            const moreTabLink = tabsList.querySelector('.ibexa-tabs__tab--more .nav-link');
            const contextMenu = moreTab.querySelector('.ibexa-tabs__context-menu');

            moreTabLink.addEventListener('click', () => {
                // moreTab.classList.toggle('ibexa-tabs__tab--expanded');
                contextMenu.classList.toggle('ibexa-tabs__context-menu--hidden');
            });
        }
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
        const tabsList = primaryTabsList.querySelector(SELECTOR_TABS_LIST);
        const moreTab = tabsList.querySelector('.ibexa-tabs__tab--more');

        if (moreTab) {
            const moreTabLink = tabsList.querySelector('.ibexa-tabs__tab--more .nav-link');
            const popupMenu = moreTab.querySelector('.ibexa-tabs__context-menu');

            const popupMenuItems = popupMenu.querySelectorAll('.ibexa-context-menu__item');

            popupMenuItems.forEach((popupMenuItem) => {
                popupMenuItem.addEventListener('click', () => {
                    const tabLinkId = popupMenuItem.dataset.tabLinkId;
                    const tabToShow = tabsList.querySelector(`.ibexa-tabs__link#${tabLinkId}`);

                    popupMenu.classList.add('ibexa-tabs__context-menu--hidden');
                    $(tabToShow).tab('show');
                    adaptTabs();
                });
            });

            // moreTabLink.addEventListener('click', () => {
            //     // moreTab.classList.toggle('ibexa-tabs__tab--expanded');
            //     popupMenu.classList.toggle('ibexa-tabs__context-menu--hidden');
            // });
        }
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((primaryTabsList) => {
        const tabsList = primaryTabsList.querySelector(SELECTOR_TABS_LIST);
        const moreTab = tabsList.querySelector('.ibexa-tabs__tab--more');
        const primaryTabsLinks = [...tabsList.querySelectorAll('.ibexa-tabs__link:not(.ibexa-tabs__tab--more)')];

        if (moreTab) {
            // const secondaryTabsList = moreTab.querySelector('.ibexa-tabs--secondary');
            // const secondaryTabsLinks = [...secondaryTabsList.querySelectorAll('.ibexa-tabs__tab .nav-link')];

            primaryTabsLinks.forEach((tabLink) => {
                $(tabLink).on('shown.bs.tab', (event) => {
                    // handleTabClick(event, secondaryTabsLinks);
                    adaptTabs();
                });
            });

            // secondaryTabsLinks.forEach((tabLink) => {
            //     tabLink.addEventListener('click', (event) => {
            //         handleTabClick(event, primaryTabsLinks);

            //         moreTab.classList.toggle('ibexa-tabs__tab--expanded', false);
            //         secondaryTabsList.classList.toggle('ibexa-tabs--hidden', true);
            //     });
            // });
        }
    });

    document.body.addEventListener('ez-content-tree-resized', adaptTabs);
    window.addEventListener('resize', adaptTabs);
})(window, window.document, window.jQuery, window.Translator);
