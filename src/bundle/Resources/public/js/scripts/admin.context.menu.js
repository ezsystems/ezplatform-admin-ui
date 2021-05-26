(function(global, doc, eZ) {
    const adapatItemsContainer = doc.querySelector('.ibexa-context-menu');

    if (adapatItemsContainer) {
        const itemsTrigger = adapatItemsContainer.querySelectorAll('[data-related-button-id]');
        const popupMenuItems = [...adapatItemsContainer.querySelectorAll('.ibexa-popup-menu__item')];
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

                popupMenuItems.forEach((popupMenuItem) => {
                    popupMenuItem.classList.toggle(
                        'ibexa-popup-menu__item--hidden',
                        !hiddenButtonsIds.includes(popupMenuItem.dataset.relatedButtonId)
                    );
                });
            },
        });
        const togglePopup = () => {
            const popup = doc.querySelector('.ibexa-context-menu .ibexa-popup-menu');

            popup.classList.toggle('ibexa-popup-menu--hidden');
        };
        const triggerContextMenuClick = (event) => {
            const { relatedButtonId } = event.currentTarget.dataset;
            const button = doc.getElementById(relatedButtonId);

            button.click();
        };

        adaptiveItems.adapt();

        doc.querySelector('.ibexa-context-menu .ibexa-btn--more').addEventListener('click', togglePopup, false);

        itemsTrigger.forEach((itemTrigger) => {
            itemTrigger.addEventListener('click', triggerContextMenuClick, false);
        });

        global.addEventListener('resize', () => adaptiveItems.adapt(), false);
    }
})(window, window.document, window.eZ);
