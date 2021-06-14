(function (global, doc, $, Translator) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    const TABS_SELECTOR = '.ibexa-tabs';
    const SELECTOR_TABS_LIST = '.ibexa-tabs__list';
    const SELECTOR_TAB_MORE = '.ibexa-tabs__tab--more';
    const copyTabs = () => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
            const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
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
        doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
            const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
            const tabs = [...tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more)')];
            const tabsLinks = [...tabsList.querySelectorAll(':scope > .ibexa-tabs__tab:not(.ibexa-tabs__tab--more) .nav-link')];
            const tabMore = tabsList.querySelector(':scope > .ibexa-tabs__tab--more');

            if (tabMore) {
                [tabMore, ...tabs].forEach((tab) => tab.classList.remove('ibexa-tabs__tab--hidden'));

                const contextMenuItems = [...tabMore.querySelectorAll('.ibexa-context-menu__item')];
                const activeTabLink = tabsLinks.find((tabLink) => tabLink.classList.contains('active'));
                const activeTab = activeTabLink ? activeTabLink.closest('.ibexa-tabs__tab') : null;
                const activeTabWidth = activeTab ? activeTab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
                const tabMoreWidth = tabMore.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
                const maxTotalWidth = tabsList.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
                const hiddenPrimaryTabsIds = [];
                let currentWidth = tabMoreWidth + activeTabWidth;

                for (let i = 0; i < tabs.length; i++) {
                    const tab = tabs[i];
                    const tabLink = tab.querySelector('.nav-link');

                    if (tabLink === activeTabLink) {
                        continue;
                    }

                    const isLastTab = i === tabs.length - 1;
                    const allPreviousTabsVisible = hiddenPrimaryTabsIds.length === 0;
                    const fitsInsteadOfMoreTab =
                        tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR < maxTotalWidth - currentWidth + tabMoreWidth;

                    if (isLastTab && allPreviousTabsVisible && fitsInsteadOfMoreTab) {
                        break;
                    }

                    if (tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR > maxTotalWidth - currentWidth) {
                        hiddenPrimaryTabsIds.push(tab.dataset.linkId);
                    }

                    currentWidth += tab.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
                }

                tabs.forEach((tab) => {
                    tab.classList.toggle('ibexa-tabs__tab--hidden', hiddenPrimaryTabsIds.includes(tab.dataset.linkId));
                });
                contextMenuItems.forEach((item) => {
                    item.classList.toggle('ibexa-context-menu__item--hidden', !hiddenPrimaryTabsIds.includes(item.dataset.tabLinkId));
                });

                tabMore.classList.toggle('ibexa-tabs__tab--hidden', !hiddenPrimaryTabsIds.length);
            }
        });
    };
    const handleClickOutsidePopupMenu = (event) => {
        doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
            const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
            const tabMore = tabsList.querySelector('.ibexa-tabs__tab--more');

            if (tabMore) {
                const popupMenu = tabMore.querySelector('.ibexa-tabs__context-menu');
                const isPopupMenuExpanded = !popupMenu.classList.contains('ibexa-tabs__context-menu--hidden');
                const isClickInsideTabMore = event.target.closest('.ibexa-tabs__tab--more');

                if (!isPopupMenuExpanded || isClickInsideTabMore) {
                    return;
                }

                popupMenu.classList.add('ibexa-tabs__context-menu--hidden');
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

    copyTabs();
    adaptTabs();

    doc.addEventListener('click', handleClickOutsidePopupMenu);

    doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
        const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
        const moreTab = tabsList.querySelector('.ibexa-tabs__tab--more');

        if (moreTab) {
            const tabMoreLink = tabsList.querySelector('.ibexa-tabs__tab--more .nav-link');
            const popupMenu = moreTab.querySelector('.ibexa-tabs__context-menu');

            tabMoreLink.addEventListener('click', () => {
                popupMenu.classList.toggle('ibexa-tabs__context-menu--hidden');
            });

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
        }
    });

    doc.querySelectorAll(TABS_SELECTOR).forEach((tabsContainer) => {
        const tabsList = tabsContainer.querySelector(SELECTOR_TABS_LIST);
        const tabMore = tabsList.querySelector('.ibexa-tabs__tab--more');
        const tabsLinks = [...tabsList.querySelectorAll('.ibexa-tabs__link:not(.ibexa-tabs__tab--more)')];

        if (tabMore) {
            tabsLinks.forEach((tabLink) => {
                $(tabLink).on('shown.bs.tab', () => {
                    adaptTabs();
                });
            });
        }
    });

    document.body.addEventListener('ez-content-tree-resized', adaptTabs);
    window.addEventListener('resize', adaptTabs);
})(window, window.document, window.jQuery, window.Translator);
