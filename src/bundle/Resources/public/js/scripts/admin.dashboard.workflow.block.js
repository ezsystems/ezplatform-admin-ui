(function(global, doc, eZ, $) {
    const container = doc.querySelector('#ez-tab-dashboard-my-workflow');
    const showPopup = ({ currentTarget: btn }) =>
        $(`[data-workflow-popup="${btn.dataset.contentId}-${btn.dataset.versionNo}"]`).modal('show');

    container.querySelectorAll('.ez-btn--workflow-chart').forEach((btn) => {
        btn.addEventListener('click', showPopup, false);
    });
})(window, window.document, window.eZ, window.jQuery);
