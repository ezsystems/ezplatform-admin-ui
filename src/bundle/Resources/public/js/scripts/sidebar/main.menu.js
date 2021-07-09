(function(global, doc) {
    const POPUP_MENU_LEFT_OFFSET = 16; 
    const allPopups = [];
    const toggleMenu = (event) => {
        const { toggleTargetSelector } = event.currentTarget.dataset;

        doc.querySelector(toggleTargetSelector).classList.toggle('ibexa-main-menu__navbar--collapsed');
    }
    const toggleRightNavbarHover = (event) => {
        const { togglerLevel, toggleTargetSelector } = event.currentTarget.dataset;
        
        if (togglerLevel != 2) {
            return;
        }

        doc.querySelector(toggleTargetSelector).classList.toggle('ibexa-main-menu__navbar--hover-toggler');
    }
    const fitSideMenu = (event) => {
        // const diff = HEADER_HEIGHT
        // console.log(event.currentTarget.scrollY)
    }

    doc.querySelectorAll('.ibexa-main-menu__toggler').forEach(button => {
        button.addEventListener('click', toggleMenu, false);
        button.addEventListener('mouseenter', toggleRightNavbarHover, false);
        button.addEventListener('mouseleave', toggleRightNavbarHover, false);
    })

    doc.querySelectorAll('.ibexa-main-menu__tooltip-trigger').forEach(button => {
        const { popupTargetSelector } = button.dataset;
        const popupNode = doc.querySelector(popupTargetSelector);

        allPopups.push(
            new eZ.core.PopupMenu({
                popupMenuElement: popupNode,
                triggerElement: button,
                position: () => {
                    const { top, left, width } = button.getBoundingClientRect();

                    popupNode.style.top = `${top}px`;
                    popupNode.style.left = `${left  + width + POPUP_MENU_LEFT_OFFSET}px`;
                },
            })
        );
    })

    global.addEventListener('scroll', fitSideMenu, false);
})(window, window.document);
