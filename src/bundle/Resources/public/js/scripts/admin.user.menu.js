(function(global, doc) {
    const userMenuContainer = doc.querySelector('.ibexa-main-header__user-menu-column');

    if (!userMenuContainer) {
        return;
    }

    const triggerElement = userMenuContainer.querySelector('.ibexa-header-user-menu__name');
    const popupMenuElement = userMenuContainer.querySelector('.ibexa-popup-menu');
    const popupMenu = new eZ.core.PopupMenu({
        triggerElement,
        popupMenuElement,
    });
})(window, window.document);
