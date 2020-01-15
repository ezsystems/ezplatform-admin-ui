(function(global, doc, eZ, $) {
    const parse = () => {
        const tooltipsNode = doc.querySelectorAll('[title]');

        for (tooltipNode of tooltipsNode) {
            if (tooltipNode.title.length) {
                const delay = {
                    show: tooltipNode.dataset.delayShow || 150,
                    hide: tooltipNode.dataset.delayHide || 75,
                };
                const extraClasses = tooltipNode.dataset.extraClasses || '';

                $(tooltipNode).tooltip({
                    delay,
                    template: `<div class="tooltip ez-tooltip ${extraClasses}">
                                    <div class="arrow ez-tooltip__arrow"></div>
                                    <div class="tooltip-inner ez-tooltip__inner"></div>
                               </div>`,
                });
            }
        }
    };

    eZ.addConfig('helpers.tooltips', {
        parse,
    });
})(window, window.document, window.eZ, window.jQuery);
