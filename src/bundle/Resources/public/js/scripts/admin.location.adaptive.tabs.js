(function (global, doc, $) {
    const TABS_SELECTOR = '.ibexa-tabs';
    const SELECTOR_TABS_LIST = '.ibexa-tabs__list';
    const allAdaptiveItems = [];
    const copyTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
            const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
            const tabs = tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more)');
            const tabMore = tabsList.querySelector(':scope > .ibexa-tabs__tab--more');
            const popupMenu = tabMore.querySelector('.ibexa-popup-menu');
            const tabTemplate = tabMore.dataset.itemTemplate;
            const fragment = doc.createDocumentFragment();

            tabs.forEach((tab) => {
                const tabLink = tab.querySelector('.ibexa-tabs__link');
                const tabLinkLabel = tabLink.textContent;
                const container = doc.createElement('ul');
                const renderedItem = tabTemplate.replace('{{ label }}', tabLinkLabel);

                container.insertAdjacentHTML('beforeend', renderedItem);

                const popupMenuItem = container.querySelector('li');

                popupMenuItem.dataset.tabLinkId = tabLink.id;
                fragment.append(popupMenuItem);
            });

            popupMenu.innerHTML = '';
            popupMenu.append(fragment);
        });
    };
    const adaptAlltabs = () => {
        allAdaptiveItems.forEach((adaptiveItems) => {
            adaptiveItems.adapt();
        });
    };
    const handleClickOutsidePopupMenu = (event) => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
            const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
            const tabMore = tabsList.querySelector('.ibexa-tabs__tab--more');

            if (tabMore) {
                const popupMenu = tabMore.querySelector('.ibexa-tabs__popup-menu');
                const isPopupMenuExpanded = !popupMenu.classList.contains('ibexa-tabs__popup-menu--hidden');
                const isClickInsideTabMore = event.target.closest('.ibexa-tabs__tab--more');

                if (!isPopupMenuExpanded || isClickInsideTabMore) {
                    return;
                }

                popupMenu.classList.add('ibexa-tabs__popup-menu--hidden');
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

    copyTabs();

    doc.querySelectorAll('.ibexa-tabs__toggler').forEach((toggler) => {
        toggler.addEventListener('click', toggleContainer);
    });

    doc.addEventListener('click', handleClickOutsidePopupMenu);

    doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
        const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
        const moreTab = tabsList.querySelector('.ibexa-tabs__tab--more');

        if (moreTab) {
            const tabMoreLink = tabsList.querySelector('.ibexa-tabs__tab--more .nav-link');
            const popupMenu = moreTab.querySelector('.ibexa-tabs__popup-menu');

            tabMoreLink.addEventListener('click', () => {
                popupMenu.classList.toggle('ibexa-tabs__popup-menu--hidden');
            });

            const popupMenuItems = popupMenu.querySelectorAll('.ibexa-popup-menu__item');

            popupMenuItems.forEach((popupMenuItem) => {
                popupMenuItem.addEventListener('click', () => {
                    const tabLinkId = popupMenuItem.dataset.tabLinkId;
                    const tabToShow = tabsList.querySelector(`.ibexa-tabs__link#${tabLinkId}`);

                    popupMenu.classList.add('ibexa-tabs__popup-menu--hidden');
                    $(tabToShow).tab('show');
                });
            });
        }
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
        const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
        const tabMore = tabsList.querySelector('.ibexa-tabs__tab--more');

        if (tabMore) {
            const tabs = [...tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more)')];
            const tabsLinks = [...tabsList.querySelectorAll('.ibexa-tabs__link:not(.ibexa-tabs__tab--more)')];
            const popupMenuItems = [...tabMore.querySelectorAll('.ibexa-popup-menu__item')];

            const adaptiveItems = new eZ.core.AdaptiveItems({
                items: tabs,
                selectorItem: tabMore,
                itemHiddenClass: 'ibexa-tabs__tab--hidden',
                container: tabsList,
                getActiveItem: () => {
                    const activeTabLink = tabsLinks.find((tabLink) => tabLink.classList.contains('active'));
                    const activeTab = activeTabLink ? activeTabLink.closest('.ibexa-tabs__tab') : null;

                    return activeTab;
                },
                onAdapted: (visibleTabs, hiddenTabs) => {
                    const hiddenTabsLinksIds = [...hiddenTabs].map((tab) => tab.dataset.linkId);

                    popupMenuItems.forEach((popupMenuItem) => {
                        popupMenuItem.classList.toggle(
                            'ibexa-popup-menu__item--hidden',
                            !hiddenTabsLinksIds.includes(popupMenuItem.dataset.tabLinkId)
                        );
                    });
                },
            });

            allAdaptiveItems.push(adaptiveItems);
            adaptiveItems.adapt();

            tabsLinks.forEach((tabLink) => {
                $(tabLink).on('shown.bs.tab', () => {
                    adaptiveItems.adapt();
                });
            });
        }
    });

    document.body.addEventListener('ez-content-tree-resized', adaptAlltabs);
    window.addEventListener('resize', adaptAlltabs);
})(window, window.document, window.jQuery);
