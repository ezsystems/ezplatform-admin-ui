(function(global, doc, eZ, $) {
    const TOOLTIPS_SELECTOR = '[title]';
    const parse = () => {
        const tooltipsNode = doc.querySelectorAll(TOOLTIPS_SELECTOR);

        for (tooltipNode of tooltipsNode) {
            if (tooltipNode.title) {
                const delay = {
                    show: tooltipNode.dataset.delayShow || 150,
                    hide: tooltipNode.dataset.delayHide || 75,
                };
                const extraClasses = tooltipNode.dataset.extraClasses || '';
                const placement = tooltipNode.dataset.placement || 'bottom';

                $(tooltipNode).tooltip({
                    delay,
                    placement,
                    template: `<div class="tooltip ez-tooltip ${extraClasses}">
                                    <div class="arrow ez-tooltip__arrow"></div>
                                    <div class="tooltip-inner ez-tooltip__inner"></div>
                               </div>`,
                });
            }
        }
    };
    const hideAll = () => {
        const tooltipsNode = doc.querySelectorAll(TOOLTIPS_SELECTOR);

        for (tooltipNode of tooltipsNode) {
            $(tooltipNode).tooltip('hide');
        }
    };

    eZ.addConfig('helpers.tooltips', {
        parse,
        hideAll,
    });
})(window, window.document, window.eZ, window.jQuery);
