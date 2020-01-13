(function(global, doc, eZ, $) {
    const parse = () => {
        const tooltipsNode = doc.querySelectorAll('[title]');

        for (tooltipNode of tooltipsNode) {
            if (tooltipNode.title.length) {
                const delay = {
                    show: tooltipNode.getAttribute('data-delay-show') || 150,
                    hide: tooltipNode.getAttribute('data-delay-hide') || 75,
                };
                const extraClasses = tooltipNode.getAttribute('data-extra-classes') || '';

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
