(function(global, doc) {
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

    doc.querySelectorAll('.ibexa-main-menu__toggler').forEach(button => {
        button.addEventListener('click', toggleMenu, false);
        button.addEventListener('mouseenter', toggleRightNavbarHover, false);
        button.addEventListener('mouseleave', toggleRightNavbarHover, false);
    })

    doc.querySelectorAll('.ibexa-main-menu__tooltip-trigger').forEach(button => {
        const popupNode = doc.querySelector(button.dataset.popupTargetSelector)

        new eZ.core.PopupMenu({
            popupMenuElement: popupNode,
            triggerElement: button,
            popupHiddenClass: 'ibexa-main-menu__popup--hidden',
            position: () => {
                popupMenuElement.style.right = 0;
            },
        });
    })
})(window, window.document);
