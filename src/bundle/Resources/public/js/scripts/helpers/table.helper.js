(function (global, doc, eZ) {
    const onChangeHandler = (event) => {
        const { activeClass, checked } = event.target;
        const action = checked ? 'add' : 'remove';
        const parentRow = event.target.closest('tr');

        parentRow.classList[action](activeClass);
    };

    const parseCheckbox = (checkboxSelector, activeClass) => {
        doc.querySelectorAll(checkboxSelector).forEach((checkboxNode) => {
            checkboxNode.activeClass = activeClass;
            checkboxNode.addEventListener('change', onChangeHandler, false);
        });
    };

    eZ.addConfig('helpers.table', {
        parseCheckbox,
    });
})(window, window.document, window.eZ);
