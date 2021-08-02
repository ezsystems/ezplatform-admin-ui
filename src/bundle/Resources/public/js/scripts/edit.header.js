(function (global, doc, eZ) {
    const SCROLL_POSITION_TO_FIT = 50;
    const headerNode = doc.querySelector('.ibexa-edit-header');
    const contentNode = doc.querySelector('.ibexa-edit-content');
    const contextMenuNode = headerNode.querySelector('.ibexa-context-menu');
    const tooltipNode = headerNode.querySelector('.ibexa-edit-header__tooltip');
    const fitHeader = (event) => {
        const { scrollTop } = event.currentTarget;
        const shouldHeaderBeSlim = scrollTop > SCROLL_POSITION_TO_FIT;

        headerNode.classList.toggle('ibexa-edit-header--slim', shouldHeaderBeSlim);
    };
    const fitTooltipTrigger = (event) => {
        const firstContextMenuItem = contextMenuNode.querySelector('.ibexa-context-menu__item');
        const leftPosition = firstContextMenuItem.offsetLeft;

        tooltipNode.style.marginLeft = `${leftPosition}px`;
    };

    contentNode.addEventListener('scroll', fitHeader, false);

    if (tooltipNode) {
        global.addEventListener('resize', fitTooltipTrigger, false);
        fitTooltipTrigger();
    }
})(window, window.document, window.eZ);
