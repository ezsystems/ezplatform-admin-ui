(function(global, doc, eZ, bootstrap) {
    const TABS_SELECTOR = '.ibexa-tabs';
    const SELECTOR_TABS_LIST = '.ibexa-tabs__list';
    const SELECTOR_TAB_MORE = '.ibexa-tabs__tab--more';
    const allAdaptiveItems = [];
    const allPopupMenus = [];
    let animationFrame = null;
    const adaptTabsAndPopupMenu = () => {
        allAdaptiveItems.forEach((adaptiveItems) => {
            adaptiveItems.adapt();
        });
        allPopupMenus.forEach((popupMenu) => {
            popupMenu.updatePosition();
        });
    };
    const handleTabsConainterChange = () => {
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
        }

        animationFrame = requestAnimationFrame(adaptTabsAndPopupMenu);
    };
    const toggleContainer = (event) => {
        const toggler = event.target;
        const header = toggler.closest('.ez-header');
        const headerContainer = header.parentElement;
        const tabContent = headerContainer.querySelector('.ibexa-tab-content');
        const isTabContentRolledUp = toggler.classList.contains('ibexa-tabs__toggler--rolled-up');

        toggler.classList.toggle('ibexa-tabs__toggler--rolled-up');
        tabContent.style.height = isTabContentRolledUp ? '' : '0px';
    };

    doc.querySelectorAll('.ibexa-tabs__toggler').forEach((toggler) => {
        toggler.addEventListener('click', toggleContainer, false);
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
        const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
        const tabMore = tabsList.querySelector(SELECTOR_TAB_MORE);

        if (tabMore) {
            const tabs = [...tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more)')];
            const tabsLinks = [...tabsList.querySelectorAll('.ibexa-tabs__link:not(.ibexa-tabs__tab--more)')];
            const popupMenuElement = tabsContainer.querySelector('.ibexa-popup-menu');

            const popupMenu = new eZ.core.PopupMenu({
                popupMenuElement,
                triggerElement: tabMore,
                onItemClick: (event) => {
                    const itemElement = event.currentTarget;
                    const tabLinkId = itemElement.dataset.tabLinkId;
                    const tabToShow = tabsList.querySelector(`.ibexa-tabs__link#${tabLinkId}`);

                    bootstrap.Tab.getOrCreateInstance(tabToShow).show();
                },
                position: () => {
                    const popupMenuLeftPosition =
                        tabMore.offsetLeft + tabsList.offsetLeft - popupMenuElement.offsetWidth + tabMore.offsetWidth + 20;

                    popupMenuElement.style.left = `${popupMenuLeftPosition}px`;
                },
            });

            const popupItemsToGenerate = tabs.map((tab) => {
                const tabLink = tab.querySelector('.ibexa-tabs__link');
                const tabLinkLabel = tabLink.textContent;

                return {
                    label: tabLinkLabel,
                    tabLinkId: tabLink.id,
                };
            });
            popupMenu.generateItems(popupItemsToGenerate, (itemElement, item) => {
                itemElement.dataset.tabLinkId = item.tabLinkId;
            });

            allPopupMenus.push(popupMenu);
            popupMenu.updatePosition();

            const adaptiveItems = new eZ.core.AdaptiveItems({
                itemHiddenClass: 'ibexa-tabs__tab--hidden',
                container: tabsList,
                getActiveItem: () => {
                    const activeTabLink = tabsLinks.find((tabLink) => tabLink.classList.contains('active'));
                    const activeTab = activeTabLink ? activeTabLink.closest('.ibexa-tabs__tab') : null;

                    return activeTab;
                },
                onAdapted: (visibleTabsWithoutSelector, hiddenTabsWithoutSelector) => {
                    const hiddenTabsLinksIds = [...hiddenTabsWithoutSelector].map((tab) => tab.querySelector('.ibexa-tabs__link').id);

                    popupMenu.toggleItems((popupMenuItem) => !hiddenTabsLinksIds.includes(popupMenuItem.dataset.tabLinkId));
                },
            });

            allAdaptiveItems.push(adaptiveItems);
            adaptiveItems.adapt();

            tabsLinks.forEach((tabLink) => {
                tabLink.addEventListener('shown.bs.tab', () => {
                    adaptiveItems.adapt();
                });
            });
        }
    });

    doc.querySelectorAll('.ibexa-tabs__link').forEach((tabLink) => {
        const tab = tabLink.parentElement;

        tabLink.addEventListener('focus', () => {
            tab.focus();
        });
    });

    doc.body.addEventListener('ez-content-tree-resized', handleTabsConainterChange, false);
    window.addEventListener('resize', handleTabsConainterChange, false);
})(window, window.document, window.eZ, window.bootstrap);
