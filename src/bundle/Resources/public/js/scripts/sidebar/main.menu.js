(function(global, doc, eZ, localStorage) {
    const HEADER_HEIGHT = 72;
    const RESIZER_WIDTH = 10;
    const TOGGLER_HEIGHT = 48;
    const SECOND_LEVEL_COLLAPSED_WIDTH = 48;
    const SECOND_LEVEL_EXPANDED_WIDTH = 220;
    const SECOND_LEVEL_MANUAL_RESIZE_MIN_WIDTH = 80;
    const mainMenuNode = doc.querySelector('.ibexa-main-menu');

    if (!mainMenuNode) {
        return;
    }

    const firstLevelMenuNode = mainMenuNode.querySelector('.ibexa-main-menu__navbar--first-level');
    const secondLevelMenuNode = mainMenuNode.querySelector('.ibexa-main-menu__navbar--second-level');
    let resizeStartPositionX = 0;
    let secondMenuLevelCurrentWidth = secondLevelMenuNode.getBoundingClientRect().width;
    const showSecondLevelMenu = (event) => {
        const { toggle } = event.currentTarget.dataset;

        if (!event.currentTarget.dataset.toggle) {
            return;
        }

        firstLevelMenuNode.classList.add('ibexa-main-menu__navbar--collapsed');
        secondLevelMenuNode.classList.remove('ibexa-main-menu__navbar--hidden');

        parseMenuTitles();
        setWidthOfSecondLevelMenu();
    };
    const setWidthOfSecondLevelMenu = () => {
        if (!localStorage.getItem('secondLevelMenuWidth') || secondLevelMenuNode.classList.contains('ibexa-main-menu__navbar--hidden')) {
            return;
        }

        const { secondLevelMenuWidth } = localStorage;
        const secondLevelMenuListWidth = secondLevelMenuWidth - RESIZER_WIDTH;

        secondLevelMenuNode.style.width = `${secondLevelMenuWidth}px`;
        secondLevelMenuNode.querySelectorAll('.ibexa-main-menu__tab-pane .ibexa-main-menu__items-list').forEach((itemList) => {
            itemList.style.width = `${secondLevelMenuListWidth}px`;
        });
        secondLevelMenuNode.classList.toggle(
            'ibexa-main-menu__navbar--collapsed',
            secondLevelMenuWidth <= SECOND_LEVEL_MANUAL_RESIZE_MIN_WIDTH
        );
    };
    const toggleSecondLevelMenu = () => {
        const newMenuWidth = secondLevelMenuNode.classList.contains('ibexa-main-menu__navbar--collapsed')
            ? SECOND_LEVEL_EXPANDED_WIDTH
            : SECOND_LEVEL_COLLAPSED_WIDTH;

        localStorage.secondLevelMenuWidth = newMenuWidth;
        setWidthOfSecondLevelMenu();
    };
    const parsePopup = (button) => {
        const { popupTargetSelector } = button.dataset;
        const popupNode = doc.querySelector(popupTargetSelector);

        if (!popupNode) {
            return;
        }

        const popup = new eZ.core.PopupMenu({
            popupMenuElement: popupNode,
            triggerElement: button,
        })
    };
    const parseMenuTitles = () => {
        firstLevelMenuNode.querySelectorAll('.ibexa-main-menu__item').forEach((item) => {
            const label = item.querySelector('.ibexa-main-menu__item-text-column').textContent;

            if (firstLevelMenuNode.classList.contains('ibexa-main-menu__navbar--collapsed')) {
                item.setAttribute('title', label);
            } else {
                item.removeAttribute('data-original-title');
            }

            eZ.helpers.tooltips.parse(mainMenuNode);
            item.removeAttribute('title');
        });

        
        eZ.helpers.tooltips.hideAll();
    };
    const addResizeListeners = ({ clientX }) => {
        resizeStartPositionX = clientX;
        secondLevelMenuNode.classList.add('ibexa-main-menu__navbar--resizing');
        secondMenuLevelCurrentWidth = secondLevelMenuNode.getBoundingClientRect().width;

        doc.addEventListener('mousemove', triggerSecondLevelChangeWidth, false);
        doc.addEventListener('mouseup', removeResizeListeners, false);
    };
    const removeResizeListeners = () => {
        doc.removeEventListener('mousemove', triggerSecondLevelChangeWidth, false);
        doc.removeEventListener('mouseup', removeResizeListeners, false);
        secondLevelMenuNode.classList.remove('ibexa-main-menu__navbar--resizing');
    };
    const triggerSecondLevelChangeWidth = ({ clientX }) => {
        const resizeValue = secondMenuLevelCurrentWidth + (clientX - resizeStartPositionX);
        const newMenuLevelWidth = resizeValue > SECOND_LEVEL_MANUAL_RESIZE_MIN_WIDTH ? resizeValue : SECOND_LEVEL_COLLAPSED_WIDTH;

        localStorage.secondLevelMenuWidth = newMenuLevelWidth;
        setWidthOfSecondLevelMenu();
    };
    const fitMenu = () => {
        const diff = Math.max(HEADER_HEIGHT - global.scrollY, 0);

        firstLevelMenuNode.querySelector('.ibexa-main-menu__items-list').style.top = `${diff}px`;
        firstLevelMenuNode.querySelector('.ibexa-main-menu__items-list').style.height = `${global.innerHeight - diff}px`;

        secondLevelMenuNode.querySelectorAll('.ibexa-main-menu__items-list').forEach((itemList) => {
            itemList.style.top = `${diff}px`;
            itemList.style.height = `${global.innerHeight - (diff + TOGGLER_HEIGHT)}px`;
        });
    };

    fitMenu();
    parseMenuTitles();
    setWidthOfSecondLevelMenu();

    global.addEventListener('scroll', fitMenu, false);
    global.addEventListener('resize', fitMenu, false);

    firstLevelMenuNode.querySelectorAll('.ibexa-main-menu__item-action').forEach((button) => {
        button.addEventListener('click', showSecondLevelMenu, false);
    });

    secondLevelMenuNode.querySelector('.ibexa-main-menu__toggler').addEventListener('click', toggleSecondLevelMenu, false);
    secondLevelMenuNode.querySelector('.ibexa-main-menu__resizer').addEventListener('mousedown', addResizeListeners, false);
    secondLevelMenuNode.querySelectorAll('.ibexa-main-menu__tooltip-trigger').forEach((button) => parsePopup(button));
})(window, window.document, window.eZ, window.localStorage);
