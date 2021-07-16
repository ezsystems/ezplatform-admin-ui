(function (global, doc, $, eZ) {
    const tables = doc.querySelectorAll('.ibexa-table');
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

        const STATE_CHECKED = 'CHECKED';
        const STATE_UNCHECKED = 'UNCHECKED';
        const STATE_ENABLED = 'ENABLED';
        const STATE_DISABLED = 'DISABLED';

        const subCheckboxesStates = [...subCheckboxes].map((subCheckbox) => {
            const disabledState = subCheckbox.disabled ? STATE_DISABLED : STATE_ENABLED;
            const checkedState = subCheckbox.checked ? STATE_CHECKED : STATE_UNCHECKED;

            return [disabledState, checkedState];
        });
        const areAllSubCheckboxesDisabled = subCheckboxesStates.every((state) => state[0] === STATE_DISABLED);
        const subCheckboxesStatesConsidered = areAllSubCheckboxesDisabled
            ? subCheckboxesStates
            : subCheckboxesStates.filter((state) => state[0] === STATE_ENABLED);
        const areAllSubCheckboxesChecked = subCheckboxesStatesConsidered.every((state) => state[1] === STATE_CHECKED);
        const areAllSubCheckboxesUnchecked = subCheckboxesStatesConsidered.every((state) => state[1] === STATE_UNCHECKED);

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

    tables.forEach((table) => {
        const tableRows = table.querySelectorAll('.ibexa-table__row');
        const tableBody = table.querySelector('.ibexa-table__body');
        const headCells = table.querySelectorAll('.ibexa-table__header-cell');
        const headCellsWithCheckboxes = table.querySelectorAll('.ibexa-table__header-cell--checkbox');

        headCellsWithCheckboxes.forEach((headCellsWithCheckbox) => {
            const mainCheckboxIndex = [...headCells].indexOf(headCellsWithCheckbox);
            const mainCheckbox = headCellsWithCheckbox.querySelector('.ibexa-input--checkbox');
            const subCheckboxes = tableBody.querySelectorAll(
                `.ibexa-table__cell--has-checkbox:nth-child(${mainCheckboxIndex + 1}) .ibexa-input--checkbox`
            );

            if (!mainCheckbox) {
                return;
            }

            subCheckboxes.forEach((subCheckbox) =>
                subCheckbox.addEventListener('change', setMainCheckboxState.bind(null, mainCheckbox, subCheckboxes), false)
            );
            setMainCheckboxState(mainCheckbox, subCheckboxes);

            mainCheckbox.addEventListener('change', setSubCheckboxesStates.bind(null, mainCheckbox, subCheckboxes), false);
        });
    });
})(window, window.document, window.jQuery, window.eZ);
