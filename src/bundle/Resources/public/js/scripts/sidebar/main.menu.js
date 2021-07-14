(function (global, doc, eZ) {
    const POPUP_MENU_LEFT_OFFSET = 16;
    const HEADER_HEIGHT = 72;
    const VISIBLE_MENU_STATE = 'visible';
    const HIDDEN_MENU_STATE = 'hidden';
    const COLLAPSED_MENU_STATE = 'collapsed';
    const mainMenuNode = doc.querySelector('.ibexa-main-menu');
    const firstLevelMenuNode = mainMenuNode.querySelector('.ibexa-main-menu__navbar--first-level');
    const secondLevelMenuNode = mainMenuNode.querySelector('.ibexa-main-menu__navbar--second-level');
    const allPopups = [];
    const parseMenuTitles = () => {
        firstLevelMenuNode.querySelectorAll('.ibexa-main-menu__item').forEach((item) => {
            const label = item.querySelector('.ibexa-main-menu__item-text-column').textContent;

            if (firstLevelMenuNode.classList.contains('ibexa-main-menu__navbar--collapsed')) {
                item.setAttribute('title', label);
            } else {
                item.removeAttribute('title');
                item.removeAttribute('data-original-title');
            }
        });

        eZ.helpers.tooltips.parse(mainMenuNode);
        eZ.helpers.tooltips.hideAll();
    };
    const parsePopup = (button) => {
        const { popupTargetSelector } = button.dataset;
        const popupNode = doc.querySelector(popupTargetSelector);
        console.log(button);

        if (!popupNode) {
            return;
        }

        allPopups.push(
            new eZ.core.PopupMenu({
                popupMenuElement: popupNode,
                triggerElement: button,
                // position: () => {
                //     const { top, left, width, height } = button.getBoundingClientRect();

                //     console.log(top);

                //     popupNode.style.top = `${top + height}px`;
                //     popupNode.style.left = `${left + width - POPUP_MENU_LEFT_OFFSET}px`;
                // },
            })
        );
    };
    const getNavbarState = (navbar) => {
        if (navbar.classList.contains('ibexa-main-menu__navbar--hidden')) {
            return HIDDEN_MENU_STATE;
        }

        if (navbar.classList.contains('ibexa-main-menu__navbar--collapsed')) {
            return COLLAPSED_MENU_STATE;
        }

        return VISIBLE_MENU_STATE;
    };
    const changeNavbarState = (navbar, state) => {
        if (getNavbarState(navbar) === state) {
            return;
        }

        navbar.classList.toggle('ibexa-main-menu__navbar--hidden', state === HIDDEN_MENU_STATE);
        navbar.classList.toggle('ibexa-main-menu__navbar--collapsed', state === COLLAPSED_MENU_STATE);
    };
    const toggleNavbar = (event) => {
        const button = event.currentTarget;
        const targetNavbarNode = mainMenuNode.querySelector(button.dataset.toggleTargetSelector);
        const state = getNavbarState(targetNavbarNode) === COLLAPSED_MENU_STATE ? VISIBLE_MENU_STATE : COLLAPSED_MENU_STATE;

        changeNavbarState(targetNavbarNode, state);
        parseMenuTitles();
    };
    const toggleHoveredClass = () => {
        secondLevelMenuNode.classList.toggle('ibexa-main-menu__navbar--hover-toggler');
    };
    const fitMenu = () => {
        const diff = Math.max(HEADER_HEIGHT - global.scrollY, 0);

        mainMenuNode.querySelectorAll('.ibexa-main-menu__items-list').forEach((itemList) => {
            itemList.style.top = `${diff}px`;
            itemList.style.height = `${global.innerHeight - diff}px`;
        });
    };

    parseMenuTitles();
    fitMenu();

    mainMenuNode.querySelectorAll('.ibexa-main-menu__toggler').forEach((togglerButton) => {
        togglerButton.addEventListener('click', toggleNavbar, false);

        if (togglerButton.classList.contains('ibexa-main-menu__toggler--second-level')) {
            togglerButton.addEventListener('mouseenter', toggleHoveredClass, false);
            togglerButton.addEventListener('mouseleave', toggleHoveredClass, false);
        }
    });

    firstLevelMenuNode.querySelectorAll('.ibexa-main-menu__item-action').forEach((button) => {
        button.addEventListener(
            'click',
            () => {
                changeNavbarState(firstLevelMenuNode, COLLAPSED_MENU_STATE);
                changeNavbarState(secondLevelMenuNode, VISIBLE_MENU_STATE);
                parseMenuTitles();
            },
            false
        );
    });

    secondLevelMenuNode.querySelectorAll('.ibexa-main-menu__tooltip-trigger').forEach((button) => parsePopup(button));

    global.addEventListener('scroll', fitMenu, false);
})(window, window.document, window.eZ);
