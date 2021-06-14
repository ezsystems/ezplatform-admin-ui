(function (global, doc, eZ) {
    const OFFSET_ROUNDING_COMPENSATOR = 0.5;

    class AdaptiveItems {
        constructor(config) {
            this.items = config.items;
            this.selectorItem = config.selectorItem;
            this.itemHiddenClass = config.itemHiddenClass;
            this.container = config.container;
            this.getActiveItem = config.getActiveItem;

            this.onAdapted = config.onAdapted;
        }

        adapt() {
            [this.selectorItem, ...this.items].forEach((item) => item.classList.remove(this.itemHiddenClass));

            const activeItem = this.getActiveItem();
            const activeItemWidth = activeItem ? activeItem.offsetWidth + OFFSET_ROUNDING_COMPENSATOR : 0;
            const selectorWidth = this.selectorItem.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
            const maxTotalWidth = this.container.offsetWidth - OFFSET_ROUNDING_COMPENSATOR;
            const hiddenItems = new Set();
            let currentWidth = selectorWidth + activeItemWidth;

            for (let i = 0; i < this.items.length; i++) {
                const item = this.items[i];

                if (item === activeItem) {
                    continue;
                }

                const isLastItem = i === this.items.length - 1;
                const allPreviousItemsVisible = hiddenItems.size === 0;
                const fitsInsteadOfSelector = item.offsetWidth + OFFSET_ROUNDING_COMPENSATOR < maxTotalWidth - currentWidth + selectorWidth;

                if (isLastItem && allPreviousItemsVisible && fitsInsteadOfSelector) {
                    break;
                }

                if (item.offsetWidth + OFFSET_ROUNDING_COMPENSATOR > maxTotalWidth - currentWidth) {
                    hiddenItems.add(item);
                }

                currentWidth += item.offsetWidth + OFFSET_ROUNDING_COMPENSATOR;
            }

            this.items.forEach((item) => {
                item.classList.toggle(this.itemHiddenClass, hiddenItems.has(item));
            });
            this.selectorItem.classList.toggle(this.itemHiddenClass, !hiddenItems.size);

            const visibleItems = new Set([...this.items].filter((item) => !hiddenItems.has(item)));

            this.onAdapted?.(visibleItems, hiddenItems);
        }
    }

    eZ.addConfig('core.AdaptiveItems', AdaptiveItems);
})(window, window.document, window.eZ);
