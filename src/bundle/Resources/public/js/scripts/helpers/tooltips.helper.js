(function(global, doc, eZ, $) {
    let lastInsertTooltipTarget = null;
    const TOOLTIPS_SELECTOR = '[title]';
    const observerConfig = {
        childList: true,
        subtree: true,
    };
    const observer = new MutationObserver((mutationsList) => {
        if (lastInsertTooltipTarget) {
            mutationsList.forEach((mutation) => {
                const { removedNodes } = mutation;

                if (removedNodes.length) {
                    removedNodes.forEach((removedNode) => {
                        if (removedNode.classList && !removedNode.classList.contains('ibexa-tooltip')) {
                            lastInsertTooltipTarget = null;
                            doc.querySelectorAll('.ibexa-tooltip.show').forEach((tooltipNode) => {
                                tooltipNode.remove();
                            });
                        }
                    });
                }
            });
        }
    });
    const parse = (baseElement = doc) => {
        if (!baseElement) {
            return;
        }

        const tooltipNodes = baseElement.querySelectorAll(TOOLTIPS_SELECTOR);

        for (tooltipNode of tooltipNodes) {
            if (tooltipNode.title) {
                const delay = {
                    show: tooltipNode.dataset.delayShow || 150,
                    hide: tooltipNode.dataset.delayHide || 75,
                };
                const extraClass = tooltipNode.dataset.tooltipExtraClass || '';
                const placement = tooltipNode.dataset.tooltipPlacement || 'bottom';
                const container = tooltipNode.dataset.tooltipContainerSelector
                    ? tooltipNode.closest(tooltipNode.dataset.tooltipContainerSelector)
                    : 'body';

                $(tooltipNode).tooltip({
                    delay,
                    placement,
                    container,
                    html: true,
                    template: `<div class="tooltip ibexa-tooltip ${extraClass}">
                                    <div class="arrow ibexa-tooltip__arrow"></div>
                                    <div class="tooltip-inner ibexa-tooltip__inner"></div>
                               </div>`,
                });

                $(tooltipNode).on('inserted.bs.tooltip', (event) => {
                    lastInsertTooltipTarget = event.currentTarget;
                });
            }
        }
    };
    const hideAll = (baseElement = doc) => {
        if (!baseElement) {
            return;
        }

        const tooltipsNode = baseElement.querySelectorAll(TOOLTIPS_SELECTOR);

        for (tooltipNode of tooltipsNode) {
            $(tooltipNode).tooltip('hide');
        }
    };

    observer.observe(doc.querySelector('body'), observerConfig);

    eZ.addConfig('helpers.tooltips', {
        parse,
        hideAll,
    });
})(window, window.document, window.eZ, window.jQuery);
