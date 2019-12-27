(function(doc) {
    jQuery(document).ready(() => {
        const tooltipsNode = doc.querySelectorAll('[data-toggle="tooltip"]');
        let delay, extraClass;

        for (tooltipNode of tooltipsNode) {
            delay = {
                show: tooltipNode.getAttribute('data-delay-show') || 150,
                hide: tooltipNode.getAttribute('data-delay-hide') || 75,
            };
            extraClass = tooltipNode.getAttribute('data-extra-class') || '';

            jQuery(tooltipNode).tooltip({
                delay: delay,
                template: `<div class="tooltip ez-tooltip ${extraClass}">
                                <div class="arrow ez-tooltip__arrow"></div>
                                <div class="tooltip-inner ez-tooltip__inner"></div>
                            </div>`,
            });
        }
    });
})(window.document);
