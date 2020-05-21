(function (global, doc, eZ, $) {
    const TOOLTIPS_SELECTOR = '[title]';
    const observerConfig = {
        childList: true,
        subtree: true,
    };
    const observer = new MutationObserver((mutationsList) => {
        mutationsList.forEach((mutation) => {
            const showedTooltipNode = doc.querySelector('.ez-tooltip.show');

            if (mutation.removedNodes.length && showedTooltipNode) {
                showedTooltipNode.remove();
            }
        });
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
                const extraClasses = tooltipNode.dataset.extraClasses || '';
                const placement = tooltipNode.dataset.placement || 'bottom';
                const container = tooltipNode.dataset.tooltipContainerSelector || 'body';

                $(tooltipNode).tooltip({
                    delay,
                    placement,
                    container,
                    template: `<div class="tooltip ez-tooltip ${extraClasses}">
                                    <div class="arrow ez-tooltip__arrow"></div>
                                    <div class="tooltip-inner ez-tooltip__inner"></div>
                               </div>`,
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
