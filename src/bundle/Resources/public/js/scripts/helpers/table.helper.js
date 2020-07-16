(function (global, doc, eZ, $) {
    parseCheckbox = (checkboxSelector) => {
        doc.querySelectorAll(checkboxSelector).forEach((checkboxNode) => {
            const parentRow = checkboxNode.closest('tr');
            const activeClass = parentRow.classList.contains('c-table-view-item') ? 'c-table-view-item--active' : 'ez-table__row--active';

            checkboxNode.addEventListener('change', (event) => {
                const { checked } = event.target;
                const action = checked ? 'add' : 'remove';

                parentRow.classList[action](activeClass);
            });
        });
    };

    eZ.addConfig('helpers.table', {
        parseCheckbox,
    });
})(window, window.document, window.eZ, window.jQuery);
