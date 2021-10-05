(function(global, doc, eZ) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;
    class AdaptiveItems {
        constructor(config) {
            this.container = config.container;
            this.items =
                config.items ||
                this.container.querySelectorAll(':scope > .ibexa-adaptive-items__item:not(.ibexa-adaptive-items__item--selector)');
            this.selectorItem = config.selectorItem || this.container.querySelector(':scope > .ibexa-adaptive-items__item--selector');
            this.itemHiddenClass = config.itemHiddenClass;
            this.getActiveItem = config.getActiveItem;
            this.onAdapted = config.onAdapted;
        }

        adapt() {
            [this.selectorItem, ...this.items].forEach((item) => item.classList.remove(this.itemHiddenClass));

            const activeItem = this.getActiveItem();
            const activeItemWidth = activeItem ? activeItem.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
            const selectorWidth = this.selectorItem.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
            const maxTotalWidth = this.container.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
            const hiddenItemsWithoutSelector = new Set();
            let currentWidth = selectorWidth + activeItemWidth;

            for (let i = 0; i < this.items.length; i++) {
                const item = this.items[i];
                const isForceHide = item.classList.contains('ibexa-adaptive-items__item--force-hide');

                if (isForceHide) {
                    hiddenItemsWithoutSelector.add(item);

                    continue;
                }

                if (item === activeItem) {
                    continue;
                }

                const lastItem = this.items[this.items.length - 1];
                const isLastNonactiveItem = lastItem === activeItem ? i === this.items.length - 2 : i === this.items.length - 1;
                const allPreviousItemsVisible = hiddenItemsWithoutSelector.size === 0;
                const fitsInsteadOfSelector = item.offsetWidth + OFFSET_ROUNDING_COMPENSATOR < maxTotalWidth - currentWidth + selectorWidth;

                if (isLastNonactiveItem && allPreviousItemsVisible && fitsInsteadOfSelector) {
                    break;
                }

                if (item.offsetWidth + OFFSET_ROUNDING_COMPENSATOR > maxTotalWidth - currentWidth) {
                    hiddenItemsWithoutSelector.add(item);
                }

                currentWidth += item.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
            }

            this.items.forEach((item) => {
                item.classList.toggle(this.itemHiddenClass, hiddenItemsWithoutSelector.has(item));
            });
            this.selectorItem.classList.toggle(this.itemHiddenClass, !hiddenItemsWithoutSelector.size);

            const visibleItemsWithoutSelector = new Set([...this.items].filter((item) => !hiddenItemsWithoutSelector.has(item)));

            this.onAdapted?.(visibleItemsWithoutSelector, hiddenItemsWithoutSelector);
        }
    }

    eZ.addConfig('core.AdaptiveItems', AdaptiveItems);
})(window, window.document, window.eZ);
