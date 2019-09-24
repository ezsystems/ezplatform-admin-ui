(function(global, doc) {
    const CLASS_HIDDEN = 'ez-user-menu__items--hidden';
    const SELECTOR_MENU_ITEMS = '.ez-user-menu__items';
    const SELECTOR_NAME_WRAPPER = '.ez-user-menu__name-wrapper';
    const CLASS_NAME_WRAPPER_ACTIVE = 'ez-user-menu__name-wrapper--active';
    const userMenu = doc.querySelector(SELECTOR_NAME_WRAPPER);
    const clickOutsideHandler = (event) => {
        if (event.target.closest(SELECTOR_NAME_WRAPPER)) {
            return;
        }

        doc.querySelector(SELECTOR_MENU_ITEMS).classList.add(CLASS_HIDDEN);
        userMenu.classList.remove(CLASS_NAME_WRAPPER_ACTIVE);
        doc.querySelector('body').removeEventListener('click', clickOutsideHandler, false);
    };
    const toggleMenuItems = () => {
        const menuItems = doc.querySelector('.ez-user-menu__items');
        const methodName = menuItems.classList.contains('ez-user-menu__items--hidden') ? 'addEventListener' : 'removeEventListener';

        doc.querySelector(SELECTOR_MENU_ITEMS).classList.toggle(CLASS_HIDDEN);
        userMenu.classList.toggle(CLASS_NAME_WRAPPER_ACTIVE);

        doc.querySelector('body')[methodName]('click', clickOutsideHandler, false);
    };

    if (userMenu) {
        userMenu.addEventListener('click', toggleMenuItems, false);
    }
})(window, window.document);
