(function (global, doc, eZ) {
    doc.querySelectorAll('.ez-table__cell .form-check-input').forEach((checkboxNode) => {
        const parentRow = checkboxNode.closest('tr');

        checkboxNode.addEventListener('change', (event) => {
            const { checked } = event.target;
            const action = checked ? 'add' : 'remove';

            parentRow.classList[action]('ez-table__row--active');
        });
    });
})(window, document, window.eZ);
