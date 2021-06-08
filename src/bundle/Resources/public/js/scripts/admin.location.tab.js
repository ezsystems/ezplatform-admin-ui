(function (global, doc, $) {
    const SELECTOR_TABS = '.ibexa-tabs';
    const SELECTOR_TAB = '.ibexa-tabs__tab';
    const SELECTOR_TAB_ACTIVE = '.ibexa-tabs__tab--active';
    const CLASS_TAB_ACTIVE = 'ibexa-tabs__tab--active';
    const switchActiveTabs = (currentTab, previousTab) => {
        currentTab.classList.add(CLASS_TAB_ACTIVE);

        if (previousTab) {
            previousTab.classList.remove(CLASS_TAB_ACTIVE);
        }
    };
    const changeHashForPageReload = (event) => {
        const { target, relatedTarget } = event;
        const currentTab = target.closest(SELECTOR_TAB);
        const previousTab = relatedTarget.closest(SELECTOR_TAB);

        global.location.hash = `${event.target.hash}#tab`;
        switchActiveTabs(currentTab, previousTab);
    };
    const setActiveHashTab = () => {
        const activeHashTabLink = doc.querySelector(`.ibexa-tabs a[href="#${global.location.hash.split('#')[1]}"]`);
        const activeHashTab = activeHashTabLink.closest(SELECTOR_TAB)
        const parentTabs = activeHashTab.closest(SELECTOR_TABS);
        const currentActiveTab = parentTabs.querySelector(SELECTOR_TAB_ACTIVE);

        $(activeHashTabLink).tab('show');
        switchActiveTabs(activeHashTab, currentActiveTab);
    };

    setActiveHashTab();

    $('.ibexa-tabs a').on('shown.bs.tab', changeHashForPageReload);
})(window, window.document, window.jQuery);
