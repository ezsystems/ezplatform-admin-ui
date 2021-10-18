(function(global, doc, bootstrap) {
    const SELECTOR_TABS = '.ibexa-tabs';
    const SELECTOR_TAB = '.ibexa-tabs__tab';
    const SELECTOR_TAB_ACTIVE = '.ibexa-tabs__tab--active';
    const CLASS_TAB_ACTIVE = 'ibexa-tabs__tab--active';
    const switchActiveTabs = (currentTab, previousTab) => {
        if (previousTab) {
            previousTab.classList.remove(CLASS_TAB_ACTIVE);
        }

        currentTab.classList.add(CLASS_TAB_ACTIVE);
    };
    const changeHashForPageReload = (hash) => {
        global.location.hash = `${hash}#tab`;
    };
    const handleTabShown = (event) => {
        const { target, relatedTarget } = event;
        const currentTab = target.closest(SELECTOR_TAB);
        const previousTab = relatedTarget.closest(SELECTOR_TAB);

        changeHashForPageReload(event.target.hash);
        switchActiveTabs(currentTab, previousTab);
    };
    const setActiveHashTab = () => {
        const activeHashTabLink = doc.querySelector(`.ibexa-tabs a[href="#${global.location.hash.split('#')[1]}"]`);

        if (!activeHashTabLink) {
            return;
        }

        const activeHashTab = activeHashTabLink.closest(SELECTOR_TAB);
        const parentTabs = activeHashTab.closest(SELECTOR_TABS);
        const currentActiveTab = parentTabs.querySelector(SELECTOR_TAB_ACTIVE);

        bootstrap.Tab.getOrCreateInstance(activeHashTabLink).show();

        switchActiveTabs(activeHashTab, currentActiveTab);
    };

    setActiveHashTab();

    doc.querySelectorAll('.ibexa-tabs a').forEach((tab) => tab.addEventListener('shown.bs.tab', handleTabShown));
})(window, window.document, window.bootstrap);
