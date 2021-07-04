(function(global, doc, eZ) {
    const adapatItemsContainer = doc.querySelector('.ibexa-context-menu');

    if (adapatItemsContainer) {
        const menuButtons = [...adapatItemsContainer.querySelectorAll('.ibexa-context-menu__item > .ibexa-btn:not(.ibexa-btn--more)')];
        const popupMenuElement = adapatItemsContainer.querySelector('.ibexa-popup-menu');
        const showPopupButton = adapatItemsContainer.querySelector('.ibexa-btn--more');
        const adaptiveItems = new eZ.core.AdaptiveItems({
            items: [...adapatItemsContainer.querySelectorAll('.ibexa-context-menu__item:not(.ibexa-context-menu__item--more)')],
            selectorItem: adapatItemsContainer.querySelector('.ibexa-context-menu__item--more'),
            itemHiddenClass: 'ibexa-context-menu__item--hidden',
            container: adapatItemsContainer,
            getActiveItem: () => {
                return adapatItemsContainer.querySelectorAll('.ibexa-context-menu__item')[0];
            },
            onAdapted: (visibleItems, hiddenItems) => {
                const hiddenButtonsIds = [...hiddenItems].map((item) => item.querySelector('.ibexa-btn').id);

                popupMenu.toggleItems((popupMenuItem) => !hiddenButtonsIds.includes(popupMenuItem.dataset.relatedButtonId));
            },
        });
        const popupMenu = new eZ.core.PopupMenu({
            popupMenuElement,
            triggerElement: showPopupButton,
            onItemClick: (event) => {
                const { relatedButtonId } = event.currentTarget.dataset;
                const button = doc.getElementById(relatedButtonId);

                button.click();
            },
            position: () => {
                popupMenuElement.style.right = 0;
            },
        });
        const popupItemsToGenerate = [...menuButtons].map((button) => {
            const relatedButtonId = button.id;
            const label = button.querySelector('.ez-btn__label').textContent;

            return {
                label,
                relatedButtonId,
            };
        });

        popupMenu.generateItems(popupItemsToGenerate, (itemElement, item) => {
            itemElement.dataset.relatedButtonId = item.relatedButtonId;
        });

        adaptiveItems.adapt();

        global.addEventListener('resize', () => adaptiveItems.adapt(), false);
    }
})(window, window.document, window.eZ);
