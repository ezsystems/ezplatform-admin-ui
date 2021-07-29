(function(global, doc, eZ, bootstrap) {
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
                    show: parseInt(tooltipNode.dataset.delayShow, 10) ?? 150,
                    hide: parseInt(tooltipNode.dataset.delayHide, 10) ?? 75,
                };
                const extraClass = tooltipNode.dataset.tooltipExtraClass ?? '';
                const placement = tooltipNode.dataset.tooltipPlacement ?? 'bottom';
                const container = tooltipNode.dataset.tooltipContainerSelector
                    ? tooltipNode.closest(tooltipNode.dataset.tooltipContainerSelector)
                    : 'body';
                const tooltip = new bootstrap.Tooltip(tooltipNode, {
                    delay,
                    placement,
                    container,
                    html: true,
                    template: `<div class="tooltip ibexa-tooltip ${extraClass}">
                                    <div class="arrow ibexa-tooltip__arrow"></div>
                                    <div class="tooltip-inner ibexa-tooltip__inner"></div>
                               </div>`,
                });

                tooltipNode.addEventListener('inserted.bs.tooltip', (event) => {
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
            bootstrap.Tooltip.getOrCreateInstance(tooltipNode).hide();
        }
    };

    observer.observe(doc.querySelector('body'), observerConfig);

    eZ.addConfig('helpers.tooltips', {
        parse,
        hideAll,
    });
})(window, window.document, window.eZ, window.bootstrap);
