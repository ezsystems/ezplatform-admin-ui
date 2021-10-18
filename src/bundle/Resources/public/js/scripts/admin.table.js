(function(global, doc, $, eZ) {
    const tablesWithBulkCheckbox = doc.querySelectorAll('.ibexa-table.ibexa-table--has-bulk-checkbox');
    const setMainCheckboxState = (mainCheckbox, subCheckboxes, event) => {
        const isFromJS = event?.detail?.isFromJS ?? false;

        if (isFromJS) {
            return;
        }

        const isTableEmpty = subCheckboxes.length === 0;

        if (isTableEmpty) {
            mainCheckbox.checked = false;
            mainCheckbox.disabled = true;

            return;
        }

        const areAllSubCheckboxesDisabled = subCheckboxes.every((checkbox) => checkbox.disabled);
        const subCheckboxesStatesConsidered = areAllSubCheckboxesDisabled
            ? subCheckboxes
            : subCheckboxes.filter((checkbox) => !checkbox.disabled);
        const areAllSubCheckboxesChecked = subCheckboxesStatesConsidered.every((checkbox) => checkbox.checked);
        const areAllSubCheckboxesUnchecked = subCheckboxesStatesConsidered.every((checkbox) => !checkbox.checked);

        mainCheckbox.disabled = areAllSubCheckboxesDisabled;

        if (areAllSubCheckboxesChecked) {
            mainCheckbox.checked = true;
            mainCheckbox.indeterminate = false;
        } else if (areAllSubCheckboxesUnchecked) {
            mainCheckbox.checked = false;
            mainCheckbox.indeterminate = false;
        } else {
            mainCheckbox.checked = false;
            mainCheckbox.indeterminate = true;
        }
    };
    const setSubCheckboxesStates = (mainCheckbox, subCheckboxes) => {
        subCheckboxes.forEach((subCheckbox) => {
            if (!subCheckbox.disabled) {
                subCheckbox.checked = mainCheckbox.checked;
                subCheckbox.dispatchEvent(new CustomEvent('change', { detail: { isFromJS: true } }));
            }
        });
    };
    const tablesCheckboxesChangeListeners = new Map();
    const addTableCheckboxesListeners = (table) => {
        const tableBody = table.querySelector('.ibexa-table__body');
        const headCells = table.querySelectorAll('.ibexa-table__header-cell');
        const headCellsWithCheckboxes = table.querySelectorAll('.ibexa-table__header-cell--checkbox');

        const checkboxesChangeListeners = new Map();
        headCellsWithCheckboxes.forEach((headCellsWithCheckbox) => {
            const mainCheckboxIndex = [...headCells].indexOf(headCellsWithCheckbox);
            const mainCheckbox = headCellsWithCheckbox.querySelector('.ibexa-input--checkbox');
            const subCheckboxes = tableBody.querySelectorAll(
                `.ibexa-table__cell--has-checkbox:nth-child(${mainCheckboxIndex + 1}) .ibexa-input--checkbox`
            );

            if (!mainCheckbox) {
                return;
            }

            setMainCheckboxState(mainCheckbox, [...subCheckboxes]);

            const hadleSubCheckboxesChange = setMainCheckboxState.bind(null, mainCheckbox, [...subCheckboxes]);
            const hadleMainCheckboxChange = setSubCheckboxesStates.bind(null, mainCheckbox, subCheckboxes);

            subCheckboxes.forEach((subCheckbox) => {
                subCheckbox.addEventListener('change', hadleSubCheckboxesChange, false);
                checkboxesChangeListeners.set(subCheckbox, hadleSubCheckboxesChange);
            });

            mainCheckbox.addEventListener('change', hadleMainCheckboxChange, false);
            checkboxesChangeListeners.set(mainCheckbox, hadleMainCheckboxChange);

            tablesCheckboxesChangeListeners.set(table, checkboxesChangeListeners);
        });
    };
    const removeTableCheckboxesListeners = (table) => {
        const checkboxesChangeListeners = tablesCheckboxesChangeListeners.get(table);

        checkboxesChangeListeners.forEach((changeListener, checkbox) => {
            if (checkbox) {
                checkbox.removeEventListener('change', changeListener, false);
            }
        });

        tablesCheckboxesChangeListeners.delete(table);
    };

    tablesWithBulkCheckbox.forEach((table) => {
        addTableCheckboxesListeners(table);

        table.addEventListener(
            'ibexa-refresh-main-table-checkbox',
            () => {
                removeTableCheckboxesListeners(table);
                addTableCheckboxesListeners(table);
            },
            false
        );
    });
})(window, window.document, window.jQuery, window.eZ);
