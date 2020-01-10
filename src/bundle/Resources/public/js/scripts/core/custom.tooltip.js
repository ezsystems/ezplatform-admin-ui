(function(global, doc, eZ, $) {
    eZ.helpers.tooltips = {
        parse: () => {
            const tooltipsNode = doc.querySelectorAll('[title]');
            let delay, extraClasses;

            for (tooltipNode of tooltipsNode) {
                if (tooltipNode.title.length) {
                    delay = {
                        show: tooltipNode.getAttribute('data-delay-show') || 150,
                        hide: tooltipNode.getAttribute('data-delay-hide') || 75,
                    };
                    extraClasses = tooltipNode.getAttribute('data-extra-classes') || '';

                    $(tooltipNode).tooltip({
                        delay,
                        template: `<div class="tooltip ez-tooltip ${extraClasses}">
                                            <div class="arrow ez-tooltip__arrow"></div>
                                            <div class="tooltip-inner ez-tooltip__inner"></div>
                                        </div>`,
                    });
                }
            }
        },
    };

    eZ.helpers.tooltips.parse();
})(window, window.document, window.eZ, window.jQuery);
